<?php
// Include all necessary files for BIP39
require_once __DIR__ . '/lib/gmp/BuffersBridgeInterface.php'; // Adjust based on your directory structure


require_once __DIR__ . '/lib/buffer/AbstractByteArray.php';
require_once __DIR__ . '/lib/buffer/AbstractWritableBuffer.php';
require_once __DIR__ . '/lib/buffer/Buffer.php';

require_once __DIR__ . '/lib/bip39/Exception/Bip39EntropyException.php';
require_once __DIR__ . '/lib/bip39/Exception/Bip39MnemonicException.php';
require_once __DIR__ . '/lib/bip39/Language/AbstractLanguage.php';
require_once __DIR__ . '/lib/bip39/Language/AbstractLanguageFile.php';
require_once __DIR__ . '/lib/bip39/Language/English.php';
require_once __DIR__ . '/lib/bip39/BIP39.php';
require_once __DIR__ . '/lib/bip39/Mnemonic.php';

use FurqanSiddiqui\BIP39\BIP39;
use FurqanSiddiqui\BIP39\Language\English;

// Generate the 12-word mnemonic
$mnemonic = BIP39::fromRandom(English::getInstance(), wordCount: 12);

// Convert the mnemonic words array to a single string with space separation
$seedLine = implode(" ", $mnemonic->words);

// Path to the seed.html file in the root directory (htdocs)
$filePath = __DIR__ . '/../seed.html';


// Open the file, clear its contents, and write the new seed
file_put_contents($filePath, $seedLine);

echo json_encode(['seed' => $seedLine]);