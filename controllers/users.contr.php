<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . '/models/users.model.php');

class UsersController
{
	private array $data = [];
	private string $method = '';
	private UsersModel $model;
	private string $endpoint = '';

	public function __construct()
	{
		$this->model = new UsersModel();
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->endpoint = trim(str_replace('/api/Users', '', $_SERVER['REQUEST_URI']), '/');
	}

	public function getJsonData(): bool|string
	{
		return json_encode($this->data);
	}

	public function getData(): array
	{
		return $this->data;
	}

	public function close(): void
	{
		$this->model->close();
		unset($this->model);
	}
	
	public function setConn(mysqli $conn): void
	{
		$this->model->setConn($conn);
	}

	public function setMethod(string $method): void
	{
		$this->method = $method;
	}

	public function setEndpoint(string $endpoint): void
	{
		$this->endpoint = trim(str_replace('/api/Users', '', $endpoint), '/');
	}

	public function processRequest(): bool
	{
		$target = (trim($this->endpoint) == '') ? null : $this->endpoint;

		$model = $this->model;
		$case = [
			'POST' => fn() => $model->post($target),
			'GET' => fn() => $model->get($target),
		];
		// 	'PUT' => fn() => $model->put(),
		// 	'DELETE' => fn() => $model->delete()
		// ];

		if (!array_key_exists($this->method, $case)) {
			http_response_code(501);
			$this->data['error'] = 'Method not implemented';
			$this->data['message'] = 'Method "' . $this->method . '" was not implemented.';
			return false;
		}

		$state = $case[$this->method]();
		$this->data = $model->getData();

		return $state;
	}
}
?>