<?php

##
# Use extensions.
##

use Phalcon\Mvc\Micro;

##
# Helper functions.
##

function log_print($text)
{
	file_put_contents("/var/log/apache2/api.log", $text, FILE_APPEND | LOCK_EX);
}

function update($command)
{
	$date = date(DATE_COOKIE);
	$log = system($command, $ret);

	if ($ret != 0)
		log_print("##\n# Command: $command\n# Date: $date\n# Return code: $ret\n##\n\n$log\n\n##\n# End command.\n##\n\n-------------\n\n");

	return $ret;
}

function update_db()
{
	return update("mco puppet runonce -C site::roles::db", $ret);
}

function update_cachedb()
{
	return update("mco puppet runonce -C site::roles::cachedb", $ret);
}

function update_koha()
{
	return update("mco puppet runonce -C site::roles::koha", $ret);
}

function update_proxy()
{
	return update("mco puppet runonce -C site::roles::proxy", $ret);
}

function update_all()
{
	update_db();
	update_cachedb();
	update_koha();
	update_proxy();
}

function update_schedule()
{
	
}


##
# Helper functions.
##

function prepare_statement($response, $mysqli, $query)
{
	if (!($statement = $mysqli->prepare($query)))
	{
		# 503 Internal Server Error
		# $mysqli->errno
		# $mysqli->error
	}

	return $statement;
}

function bind_param($response, $statement, $param, $value)
{
	if (!($ret = $statement->bind_param($param, $value)))
	{
		# 503 Internal Server Error
		# $statement->errno
		# $statement->error
	}

	return $ret;
}

function execute_statement($response, $statement)
{
	if (!($ret = $statement->execute()))
	{
		# 503 Internal Server Error
		# $statement->errno
		# $statement->error
	}

	return $ret;
}

##
# Parameter checking functions.
##

function opac_server_name_exists($response, $mysqli, $opac_server_name)
{
	if (!($statement = prepare_statment($response, $mysqli, "CALL opac_server_name_exists(?)"))
		return NULL;

	if (!bind_param($response, $statement, "opac_server_name", $opac_server_name))
		return NULL;

	if (!execute_statement($response, $statement))
		return NULL;

	# http://php.net/manual/en/class.mysqli-result.php
}

function intra_server_name_exists($response, $mysqli, $intra_server_name)	
{
	"CALL check_for_intra_server_name(?)"
}

##
# Status checking functions.
##

function check_db($id)
{
	return !empty(system("puppet query nodes 'Site::Roles::Db::Site[\"$id\"]'"));
}

function check_cachedb($id)
{
	return !empty(system("puppet query nodes 'Site::Roles::Cachedb::Site[\"$id\"]'"));
}

function check_koha($id)
{
	return !empty(system("puppet query nodes 'Site::Roles::Koha::Site[\"$id\"]'"));
}

function check_proxy($id)
{
	return !empty(system("puppet query nodes 'Site::Roles::Prox::Site[\"$id\"]'"));
}

##
# The REST API to collect site information from users.
##

$app = new Micro();

$app->post("/api/register", function() use($app)
{
	$respond = null;

	# Start building the response.
	$response = new Response();

	# Get the new site information from the JSON object.
	$object = json_decode($app->request->getJsonRawbody());

	$first_name = $object->{"first_name"};
	$surname = $object->{"surname"};
	$email = $object->{"email"};
	$password = $object->{"password"};
	$opac_server_name = $object->{"opac_server_name"};
	$intra_server_name = $object->{"intra_server_name"};

	# Check validity of the parameters.
	if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $respond == null)
		$respond = "Invalid email address";

	if (!filter_var($opac_server_name, FILTER_VALIDATE_URL) && $respond == null)
		$respond = "Invalid email address";

	if (!filter_var($intra_server_name, FILTER_VALIDATE_URL) && $respond == null)
		$respond = "Invalid email address";

	# If all of the parameters are valid, 
	if ($respond == null)
	{
		try
		{
			$mysqli = new mysqli($hostname, $username, $password, $database);

			##
			# Register the Koha site with the registration database.
			##
			if (!($statement = prepare_statement($response, $mysqli, "CALL add_koha_site(?)")))
				throw Exception("Unable to prepare SQL statement for adding the Koha site");

			if (!bind_param($response, $statement, "first_name", $first_name))
				throw Exception("Unable to bind the first name to the statement for adding the Koha site");

			if (!bind_param($response, $statement, "surname", $surname))
				throw Exception("Unable to bind the surname to the statement for adding the Koha site");

			if (!bind_param($response, $statement, "email", $email))
				throw Exception("Unable to bind the email address to the statement for adding the Koha site");

			if (!bind_param($response, $statement, "password", $password))
				throw Exception("Unable to bind the password to the statement for adding the Koha site");

			if (!bind_param($response, $statement, "opac_server_name", $opac_server_name))
				throw Exception("Unable to bind the OPAC server name to the statement for adding the Koha site");

			if (!bind_param($response, $statement, "intra_server_name", $intra_server_name))
				throw Exception("Unable to bind the intranet server name to the statement for adding the Koha site");

			if (!execute_statement($response, $statement))
				throw Exception("Unable to execute the statement for getting the Koha site ID");

			if
			{
				if (!($statement = prepare_statement($response, $mysqli, "CALL get_koha_site_id(?)")))
					throw Exception("Unable to prepare SQL statement for getting the Koha site ID");

				if (!bind_param($response, $statement, "opac_server_name", $opac_server_name))
					throw Exception("Unable to bind the OPAC server name to the statement for getting the Koha site ID");

				if (!bind_param($response, $statement, "intra_server_name", $intra_server_name))
					throw Exception("Unable to bind the intranet server name to the statement for getting the Koha site ID");

				if (!(execute_statement($response, $statement)))
					throw Exception("Unable to execute the statement for getting the Koha site ID");

				$statement->bind_result($id);

				# Only get the ID and send an "Accepted" reply if we got a result back.
				# 202 Accepted
				if (($results = $mysqli->fetch()))
				{
					
					if ($ret == 0)
					{
						$response->setStatusCode(202, "Accepted");
						$response->setJsonContent("id" => $id);
					}
				}
				else
					throw Exception("Unable to get Koha site ID from OPAC/intra server name");
			}
		}
		# 503 Internal Server Error
		# If the client sent correct data, the site didn't already exist and it can't
		# find the ID at this point, something else is wrong.
		catch (Exception e)
		{
			$response->setStatusCode(503, "Internal Server Error");
		}
	}
	# 400 Bad Request
	# The client sent invalid data.
	else
	{
		$response->setStatusCode(400, "Bad Request");
		$response->setJsonContent("message", $respond);
	}
});

$app->handle();

?>
