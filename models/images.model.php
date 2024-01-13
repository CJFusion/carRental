<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . '/assets/dbConnect.php');
require_once(dirname(__DIR__) . '/assets/sessionConfig.php');
require_once(dirname(__DIR__) . '/assets/sqlPrepare.php');

class ImagesModel
{
	private array $data = [];
	private mysqli|bool $conn;

	public function __construct()
	{
		$conn = mysqliConnect();
		if (!$conn) {
			http_response_code(500);
			echo json_encode(["error" => "MySql connection failed", "message" => $conn->error]);
			die();
		}
		$this->data['connStatus'] = "MySql connection successful";
		$this->conn = $conn;
	}

	public function getData(): array
	{
		return $this->data;
	}

	public function close(): void
	{
		$this->conn->close();
	}

	private function isLoggedIn(int $userId = null): bool
	{
		startSession();
		if (!isset($_SESSION['user']))
			return false;
		if ($userId !== null && $userId !== $_SESSION['userId'])
			return false;
		return true;
	}





	// 	Supports the following endpoints:
	// TODO:		/api/Images			=> To post an array of images under current logged in user
	// TODO:		/api/Images/{int}	=> To post an array of images under given userId
	//		/api/Images/CarId/{int}		=> To post an array of images for given carId under current logged in agency
	// TODO:		/api/Images/CarId/Agency/{int}/{int}	=> To post an array of images for given carId{@params_1} under agencyId{@params_2}
	function post(int|string $target = NULL): bool
	{
		$conn = $this->conn;

		if (strpos(strtolower($target), "carid") === 0) {
			$carId = array_slice(explode("/", $target), 1);

			if (!isset($carId[0])) {
				http_response_code(500);
				$this->data["error"] = "Invalid endpoint request";
				$this->data["message"] = "carId not set for current resource request";

				return false;
			}

			if (!$this->isLoggedIn())
				require_once(dirname(__DIR__) . '/assets/logout.php');
			$userId = $_SESSION['userId'];

			if (empty($_FILES["imageInput"]["name"][0])) {
				http_response_code(400);
				$this->data["error"] = "No files uploaded";
				$this->data["message"] = "Please select an image to upload";

				return false;
			}

			foreach ($_FILES['imageInput']['tmp_name'] as $key => $tmpName) {
				$fileSize = $_FILES['imageInput']['size'][$key];
				$fileName = htmlspecialchars(trim($_FILES['imageInput']['name'][$key]));
				$fileType = pathinfo($fileName, PATHINFO_EXTENSION);

				if ($fileSize > 250 * 1024) {
					http_response_code(400);
					$this->data["error"] = "File size exceeded";
					$this->data["message"] = "Image \"$fileName\" exceeds size of 250KB";

					return false;
				}

				if (!in_array(strtolower($fileType), ['jpg', 'png', 'jpeg'])) {
					http_response_code(400);
					$this->data["error"] = "Invalid file type uploaded";
					$this->data["message"] = "Sorry, only JPG, JPEG & PNG images are allowed";

					return false;
				}
			}

			// File upload directory 
			$targetDir = dirname(__DIR__) . "/uploads/userId_$userId/";
			if (!is_dir($targetDir))
				mkdir($targetDir, 0777, true);

			foreach ($_FILES['imageInput']['tmp_name'] as $key => $tmpName) {
				$fileName = basename($_FILES["imageInput"]["name"][$key]);
				$targetFilePath = $targetDir . $fileName;
				$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

				// Upload file to server 
				if (!move_uploaded_file($_FILES["imageInput"]["tmp_name"][$key], $targetFilePath)) {
					http_response_code(500);
					$this->data["error"] = "Uploading to server failed";
					$this->data["message"] = "Sorry, there was an error uploading your image \"$fileName\"";

					return false;
				}

				// Insert image file name into database 
				$sql = "INSERT INTO Images (userId, carId, fileName) VALUES (?, ?, ?)";
				$stmtExec = executePreparedStatement($conn, $sql, "iis", $userId, $carId, $fileName);

				if ($stmtExec->affected_rows !== 1) {
					http_response_code(500);
					$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
					$this->data["message"] = "Sorry, image \"$fileName\" upload failed, please try again.";

					return false;
				}
			}

			http_response_code(201);
			$this->data["message"] = "All images uploaded successfully.";
			return true;
		}

		http_response_code(400);
		$this->data['error'] = 'Invalid endpoint';
		$this->data['message'] = 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.';

		return false;
	}


	// 	Supports the following endpoints:
	// TODO:		/api/Images/Customer		=> To get an array of images of a logged in customer by customerId
	// TODO:		/api/Images/Customer/{int}	=> To get an array of images of a customer by their customerId
	// TODO:		/api/Images/Customers		=> To get arrays of images of all customers
	// TODO:		/api/Images/Agency			=> To get an array of images of a logged in agency by agencyId
	// TODO:		/api/Images/Agency/{int}	=> To get an array of images of a agency by agencyId
	// TODO:		/api/Images/Agencies		=> To get arrays of images of all agencies
	// TODO:		/api/Images/CarId/{int}		=> To get an array of images of a car by its carId under current logged in agency
	//		/api/Images/CarId/Agency/{int}/{int}	=> To get an array of images of a car by its carId{@param_1} under given agencyId{@param_2}
	function get(string $target): bool
	{
		$conn = $this->conn;

		if (strpos(strtolower($target), "carid/agency") === 0) {
			$ids = array_slice(explode("/", $target), 2);

			if (!isset($ids[0]) || !isset($ids[1])) {
				http_response_code(500);
				$this->data["error"] = "Invalid endpoint request";
				$this->data["message"] = "carId and agencyId not set for current resource request";

				return false;
			}

			$carId = $ids[0];
			$userId = $ids[1];

			$sql = "SELECT fileName FROM Images WHERE carId = ? ORDER BY uploadedOn DESC";

			$stmtExec = executePreparedStatement($conn, $sql, "i", ($carId === NULL) ? $userId : $carId);
			$sqlResult = $stmtExec->get_result();

			if ($sqlResult->num_rows < 1) {
				http_response_code(500);
				$this->data["error"] = "Failed query: " . $sql . " " . $conn->error;
				$this->data["message"] = "Sorry, images for carId: \"$carId\" could not be found";

				return false;
			}

			while ($row = $sqlResult->fetch_assoc())
				$imageUrl[] = "/uploads/userId_$userId/" . $row["fileName"];


			http_response_code(200);
			$this->data["imageUrl"] = $imageUrl;
			$this->data["message"] = "All image links retrieved successfully";

			return true;
		}

		http_response_code(400);
		$this->data['error'] = 'Invalid endpoint';
		$this->data['message'] = 'Endpont "' . $_SERVER['REQUEST_URI'] . '" does not exist.';

		return false;
	}
}
?>