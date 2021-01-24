
<?php

// required headers
 header("Content-Type: application/json; charset=UTF-8");

 $db = new PDO('sqlite:./db/projects.sqlite');
 
 $request = json_decode(file_get_contents('php://input'));

 $result = null;

 $type = $_GET['type'];

  if ($type == 'read') {
        $statement = $db->prepare('SELECT *, strftime(\'%Y-%m-%dT%H:%M:%SZ\', Start) as Start, strftime(\'%Y-%m-%dT%H:%M:%SZ\', End) as End FROM Tasks');
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        //echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

  elseif ($type == 'create') {
        // In batch mode the inserted records are available in the 'models' field
        $createdTasks = $request->models;

        // Will store the TaskID fields of the inserted records
        $result = array();

        foreach($createdTasks as $task) {
            // Create SQL INSERT statement
            $statement = $db->prepare('INSERT INTO Tasks (Title, Start, End, IsAllDay, Description, RecurrenceID, RecurrenceRule, RecurrenceException, OwnerID) VALUES (:title, :start, :end, :isAllDay, :description, :recurrenceID, :recurrenceRule, :recurrenceException, :ownerID)');

            // Bind parameter values
            $statement->bindValue(':title', $task->Title);
            $statement->bindValue(':start', $task->Start);
            $statement->bindValue(':end', $task->End);
            $statement->bindValue(':isAllDay', $task->IsAllDay);
            $statement->bindValue(':description', $task->Description);
            $statement->bindValue(':recurrenceID', $task->RecurrenceID);
            $statement->bindValue(':recurrenceRule', $task->RecurrenceRule);
            $statement->bindValue(':recurrenceException', $task->RecurrenceException);
            $statement->bindValue(':ownerID', $task->OwnerID);

            // Execute the statement
            $statement->execute();

            // Set TaskID to the last inserted ID (TaskID is auto-incremented column)
            $task->TaskID = $db->lastInsertId();

            // The result of the 'create' operation is all inserted tasks
            $result[] = $task;
        }

    }

  elseif ($type == 'update') {
        // in batch mode the updated records are available in the 'models' field
        $updatedTasks = $request->models;

        foreach($updatedTasks as $task) {
            // Create UPDATE SQL statement
            $statement = $db->prepare('UPDATE Tasks SET Title = :title, Start = :start, End = :end, IsAllDay = :isAllDay, Description = :description, RecurrenceID = :recurrenceID, RecurrenceRule = :recurrenceRule, RecurrenceException = :recurrenceException, OwnerID = :ownerID WHERE TaskID = :taskID');

            // Bind parameter values
            $statement->bindValue(':title', $task->Title);
            $statement->bindValue(':start', $task->Start);
            $statement->bindValue(':end', $task->End);
            $statement->bindValue(':isAllDay', $task->IsAllDay);
            $statement->bindValue(':description', $task->Description);
            $statement->bindValue(':recurrenceID', $task->RecurrenceID);
            $statement->bindValue(':recurrenceRule', $task->RecurrenceRule);
            $statement->bindValue(':recurrenceException', $task->RecurrenceException);
            $statement->bindValue(':ownerID', $task->OwnerID);
            $statement->bindValue(':taskID', $task->TaskID);

            // Execute the statement
            $statement->execute();
        }
    }

  elseif ($type == 'destroy') {
        // in batch mode the destroyed records are available in the 'models' field
        $destroyedTasks = $request->models;

        foreach($destroyedTasks as $task) {
            // Create DELETE SQL statement
            $statement = $db->prepare('DELETE FROM Tasks WHERE TaskID = :taskID');

            // Bind parameter values
            $statement->bindValue(':taskID', $task->TaskID);

            // Execute the statement
            $statement->execute();
        }
    }

  echo json_encode($result,JSON_UNESCAPED_UNICODE);

?>