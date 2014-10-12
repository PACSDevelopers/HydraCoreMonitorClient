<?hh


	class HC_Encryption_Test extends PHPUnit_Framework_TestCase {



		// @todo: Complete Encryption Class Test


		public function testEncryption()

		{



			$this->assertEquals(class_exists('HC\Encryption'), true);

		}



		/**
		 * @depends testEncryption
		 */

		public function testEncryptionCreation()

		{



			$this->assertNotEmpty(new HC\Encryption());

		}



		/**
		 * @depends testEncryptionCreation
		 */

		public function testEncryptionHashingMatch()

		{



			$encryption = new HC\Encryption();

			$hash1      = $encryption->hash('VALUE', ['salt' => 'SALT']);

			$hash2      = $encryption->hash('VALUE', ['salt' => 'SALT']);

			$this->assertEquals($hash1, $hash2, 'Encryption hash did not match');



		}



		/**
		 * @depends testEncryptionHashingMatch
		 */

		public function testEncryptionHashingMethods()

		{



			$encryption = new HC\Encryption();

			foreach ($encryption->getAvailableHashingMethods() as $algo) {

				$encryption = new HC\Encryption(['hashingMethod' => $algo]);

				$value      = uniqid();

				$salt       = uniqid();

				$hash1      = $encryption->hash($value, ['salt' => $salt]);

				$hash2      = $encryption->hash($value, ['salt' => $salt]);

				$this->assertEquals($hash1, $hash2, 'Encryption hash did not match');

				$this->assertEquals($encryption->getHashingMethod(), $algo);

			}



		}



		/**
		 * @depends testEncryptionHashingMethods
		 */

		public function testEncryptionHashingFile()

		{



			$encryption = new HC\Encryption();



			$file = file_put_contents('file.txt', uniqid());

			$this->assertTrue(($file !== false), 'Could not create test file');



			if ($file !== false) {

				$hash1 = $encryption->hashFile('file.txt');

				$hash2 = $encryption->hashFile('file.txt');

				$this->assertEquals($hash1, $hash2, 'Encryption hash (file) did not match');

			}

		}



		/**
		 * @depends testEncryptionHashingFile
		 */

		public function testEncryptionHashingIncremental()

		{



			$encryption = new HC\Encryption();

			$encryption->initIncrementalHash();



			$uniqueValues = [

				uniqid(),

				uniqid(),

				uniqid()

			];

			foreach ($uniqueValues as $uniqueValue) {

				$encryption->incrementHash($uniqueValue);

			}

			$hash1 = $encryption->getIncrementalHash();



			$encryption->initIncrementalHash();

			foreach ($uniqueValues as $uniqueValue) {

				$encryption->incrementHash($uniqueValue);

			}

			$hash2 = $encryption->getIncrementalHash();



			$this->assertEquals($hash1, $hash2, 'Encryption hash (incremental) did not match');

		}



		/**
		 * @depends testEncryptionHashingIncremental
		 */

		public function testEncryptionHashingIncrementalFile()

		{



			$encryption = new HC\Encryption();



			$file = file_put_contents('file1.txt', uniqid());

			$this->assertTrue(($file !== false), 'Could not create test file 1');



			$file2 = file_put_contents('file2.txt', uniqid());

			$this->assertTrue(($file2 !== false), 'Could not create test file 2');



			if (($file !== false) && ($file2 !== false)) {

				$encryption->initIncrementalHash();

				$encryption->incrementHashFile('file1.txt');

				$encryption->incrementHashFile('file2.txt');

				$hash1 = $encryption->getIncrementalHash();



				$encryption->initIncrementalHash();

				$encryption->incrementHashFile('file1.txt');

				$encryption->incrementHashFile('file2.txt');

				$hash2 = $encryption->getIncrementalHash();

				$this->assertEquals($hash1, $hash2, 'Encryption hash (incremental file) did not match');

			}

		}



		/**
		 * @depends testEncryptionCreation
		 */

		public function testEncryptionEncryptionMatch()

		{



			$encryption = new HC\Encryption();

			$encrypted  = $encryption->encrypt('VALUE', 'SALT');

			if($encrypted){

				$decrypted  = $encryption->decrypt($encrypted, 'SALT');

				$this->assertEquals($decrypted, 'VALUE', 'Encryption did not match');

			} else {

				$this->assertFalse(true, 'Failed to encrypt');

			}

		}



		/**
		 * @depends testEncryptionEncryptionMatch
		 */

		public function testEncryptionEncryptionMethods()

		{



			$encryption = new HC\Encryption();

			$methods = $encryption->getAvailableEncryptionMethods();

			if(is_array($methods)){

				foreach ($methods as $algo) {

					$encryption = new HC\Encryption(['encryptionMethod' => $algo]);

					$value      = uniqid();

					$salt       = uniqid();

					$encrypted  = $encryption->encrypt($value, $salt);

					if($encrypted){

						$decrypted  = $encryption->decrypt($encrypted, $salt);

						$this->assertEquals($value, $decrypted, 'Encryption did not match for algorithm:' . $algo);

					} else {

						$this->assertFalse(true, 'Failed to encrypt');

					}



					$this->assertEquals($encryption->getEncryptionMethod(), $algo);

				}

			} else {

				$this->assertTrue(is_array($methods));

			}



		}

	}

