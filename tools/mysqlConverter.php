<?php
//Only used for old dramiel users
function convertMysql($config)
{
    $host = $config['mysql']['host'];
    $username = $config['mysql']['user'];
    $password = $config['mysql']['password'];
    $database = $config['mysql']['dbname'];
    $mysqli = mysqli_connect($host, $username, $password, $database);

    if ($stmt = $mysqli->prepare("SELECT * FROM authUsers WHERE active='yes'")) {

        // Bind the variables to the parameter as strings.
        $stmt->bind_param('s', $authCode);

        // Execute the statement.
        $stmt->execute();

        // Return Row
        $result = $stmt->get_result();

        // Close the prepared statement.
        $stmt->close();

        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            foreach ($row as $user) {
                $access = array();
                if ($user['role'] === "corp") {
                    $access[] = 'corp';
                } else if ($user['role'] === "corp/ally") {
                    $access[] = 'corp';
                    $access[] = 'alliance';
                } else if ($user['role'] === "ally") {
                    $access[] = 'alliance';
                } else {
                    $access[] = 'character';
                }
                $accessList = json_encode($access);
                insertUser($user['characterID'], $user['discordID'], $accessList);
            }
        }
        if (!file_exists(__DIR__ . '.blocker')) {
            touch(__DIR__ . '.blocker');
        }
    }
    return null;
}