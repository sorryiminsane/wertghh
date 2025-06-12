<?php
// Check for the signal from dashboard.php
$signalFile = 'signal.txt'; // You can change the file name if needed

if (file_exists($signalFile)) {
    // Read the contents of the signal file
    $signal = file_get_contents($signalFile);

    try {
        // Remove the signal file
        unlink($signalFile);
    } catch (Exception $e) {
        // Handle any errors that occur during file removal
        echo 'Error removing signal file: ' . $e->getMessage();
    }

    // Return the signal to loading.php
    echo $signal;
} else {
    // Return an empty response if the signal file doesn't exist
    echo '';
}
?>
