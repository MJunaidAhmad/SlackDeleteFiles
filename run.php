<?php
/**
 * Created by PhpStorm.
 * User: Junaid Ahmad
 * Date: 7/28/2017
 * Time: 9:07 PM
 */
?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
    </head>
    <body>
    <form method="post" action="run.php">
        <input required name="token" type="text" placeholder="Legacy Token">
       <a target="_blank" href="https://api.slack.com/custom-integrations/legacy-tokens">Get Token Here</a>
	   <input required name="date" type="date">
        <button type="submit">Delete My Files</button>
    </form>
    </body>
    </html>
<?php require 'vendor/autoload.php';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  $client = new GuzzleHttp\Client([
    ]);
    try {
        $token = $client->request('GET', 'https://slack.com/api/auth.test?token='.$_POST['token']);
    } catch (\GuzzleHttp\Exception\ClientException $e) {

        die("exception occured in retrieving user.\n");
    }
    print('User Authentication Successfull.<br>');
    $body = $token->getBody();
    $responseToken = json_decode($body, true);
    try {
        $list = $client->request('GET', 'https://slack.com/api/files.list?token='.$_POST['token'].'&user=' . $responseToken['user_id'] . '&ts_to=' . strtotime($_POST["date"]));
    } catch (\GuzzleHttp\Exception\ClientException $e) {

        die("exception occured in retrieving list of files.<br>");
    }

    print('List Retrieve Successfull.<br>');
    $body = $list->getBody();
    $responseList = json_decode($body, true);
    print('Deleting one by one.<br>');
    foreach ($responseList['files'] as $file) {
        try {
            $res = $client->request('GET', 'https://slack.com/api/files.delete?token=xoxp-145345631858-148717225538-231141171282-a7b9440b9a2a293cfac40bf1a526bd6c&file=' . $file['id']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            die("exception occured in deleting file.<br>");
        }
        $body = $res->getBody();
        $response = json_decode($body, true);

        if ($response['ok'] == true) {
            print($responseToken['user'] . '\'s File ' . $file['name'] . ' has been deleted.<br>');
        } else {
          if($response['error']!="file_not_found") {
              print('Unable to delete ' . $responseToken['user'] . '\'s File ' . $file['name'] . '.<br>');
              print("Reason:" .$response['error'] . '.<br>');
             // print($file['id']);
             // var_dump($response);
          }
        }
    }

}
?>