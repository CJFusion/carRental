-- Fill in proper Id's for each query

INSERT INTO Cars (agencyId, model, licenseNumber, capacity, rentPerDay)
VALUES 
    -- For agencyId = 2
    (2, 'Tesla Model S', 'MH 01 AB 1234', 5, 600.00),
    (2, 'Porsche Cayenne', 'DL 02 CD 5678', 4, 550.00),
    (2, 'Mercedes-Benz GLE', 'KA 05 EF 9012', 5, 700.00),
    (2, 'BMW X5', 'TN 06 GH 3456', 4, 620.00),
    (2, 'Audi Q7', 'UP 07 IJ 7890', 5, 580.00),
    
    -- For agencyId = 11
    (11, 'Range Rover', 'GJ 08 KL 1234', 4, 800.00),
    (11, 'Bentley Bentayga', 'MH 09 MN 5678', 4, 900.00),
    (11, 'Rolls-Royce Cullinan', 'UP 10 OP 9012', 4, 850.00),
    (11, 'Ferrari Portofino', 'DL 11 QR 3456', 4, 1000.00),
    (11, 'Lamborghini Urus', 'TN 12 ST 7890', 4, 950.00),
    
    -- For agencyId = 13
    (13, 'Maserati Levante', 'KA 13 UV 1234', 5, 750.00),
    (13, 'Aston Martin DBX', 'MH 14 WX 5678', 4, 700.00),
    (13, 'McLaren GT', 'GJ 15 YZ 9012', 5, 820.00),
    (13, 'Bugatti Chiron', 'DL 16 AB 3456', 5, 1500.00),
    (13, 'Porsche 911', 'TN 17 CD 7890', 5, 1200.00);

INSERT INTO Images (userId, carId, fileName)
VALUES 
    -- For agencyId = 2
		-- For carId = 3
		(2, 3, 'teslaS_1.jpg'),
		(2, 3, 'teslaS_2.jpg'),
		(2, 3, 'teslaS_3.jpg'),
		(2, 3, 'teslaS_4.jpg'),
        
        -- For carId = 4
		(2, 4, 'porscheCayenne_1.jpg'),
		(2, 4, 'porscheCayenne_2.jpg'),
		(2, 4, 'porscheCayenne_3.jpg'),
		(2, 4, 'porscheCayenne_4.jpg'),
        
        -- For carId = 5
		(2, 5, 'MercedesBenzGle_1.jpg'),
		(2, 5, 'MercedesBenzGle_2.jpg'),
		(2, 5, 'MercedesBenzGle_3.jpg'),
		(2, 5, 'MercedesBenzGle_4.jpg'),
        
        -- For carId = 6
		(2, 6, 'bmwX5_1.jpg'),
		(2, 6, 'bmwX5_2.jpg'),
		(2, 6, 'bmwX5_3.jpg'),
		(2, 6, 'bmwX5_4.jpg'),
        
        -- For carId = 7
		(2, 7, 'audiQ7_1.jpg'),
		(2, 7, 'audiQ7_2.jpg'),
		(2, 7, 'audiQ7_3.jpg'),
		(2, 7, 'audiQ7_4.jpg');

INSERT INTO Images (userId, carId, fileName)
VALUES 
    -- For userId = 11
        -- For carId = 8
		(11, 8, 'rangeRover_1.jpg'),
		(11, 8, 'rangeRover_2.jpg'),
		(11, 8, 'rangeRover_3.jpg'),
		(11, 8, 'rangeRover_4.jpg'),
        
        -- For carId = 9
		(11, 9, 'bentleyBentayga_1.jpg'),
		(11, 9, 'bentleyBentayga_2.jpg'),
		(11, 9, 'bentleyBentayga_3.jpg'),
		(11, 9, 'bentleyBentayga_4.jpg'),
        
        -- For carId = 10
		(11, 10, 'rollsRoyceCullinan_1.jpg'),
		(11, 10, 'rollsRoyceCullinan_2.jpg'),
		(11, 10, 'rollsRoyceCullinan_3.jpg'),
		(11, 10, 'rollsRoyceCullinan_4.jpg'),
        
        -- For carId = 11
		(11, 11, 'ferrariPortofino_1.jpg'),
		(11, 11, 'ferrariPortofino_2.jpg'),
		(11, 11, 'ferrariPortofino_3.jpg'),
		(11, 11, 'ferrariPortofino_4.jpg'),
        
        -- For carId = 12
		(11, 12, 'lamborghiniUrus_1.jpg'),
		(11, 12, 'lamborghiniUrus_2.jpg'),
		(11, 12, 'lamborghiniUrus_3.jpg'),
		(11, 12, 'lamborghiniUrus_4.jpg');

INSERT INTO Images (userId, carId, fileName)
VALUES 
    -- For userId = 13
        -- For carId = 13
		(13, 13, 'maseratiLevante_1.jpg'),
		(13, 13, 'maseratiLevante_2.jpg'),
		(13, 13, 'maseratiLevante_3.jpg'),
		(13, 13, 'maseratiLevante_4.jpg'),
        
        -- For carId = 14
		(13, 14, 'astonMartinDbx_1.jpg'),
		(13, 14, 'astonMartinDbx_2.jpg'),
		(13, 14, 'astonMartinDbx_3.jpg'),
		(13, 14, 'astonMartinDbx_4.jpg'),
        
        -- For carId = 15
		(13, 15, 'mclarenGt_1.jpg'),
		(13, 15, 'mclarenGt_2.jpg'),
		(13, 15, 'mclarenGt_3.jpg'),
		(13, 15, 'mclarenGt_4.jpg'),
        
        -- For carId = 16
		(13, 16, 'bugattiChiron_1.jpg'),
		(13, 16, 'bugattiChiron_2.jpg'),
		(13, 16, 'bugattiChiron_3.jpg'),
		(13, 16, 'bugattiChiron_4.jpg'),
        
        -- For carId = 17
		(13, 17, 'porsche911_1.jpg'),
		(13, 17, 'porsche911_2.jpg'),
		(13, 17, 'porsche911_3.jpg'),
		(13, 17, 'porsche911_4.jpg');