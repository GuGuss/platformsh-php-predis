<?php
require "vendor/predis/predis/autoload.php";
Predis\Autoloader::register();

try {
    //$redis = new Predis\Client();

	if (getenv('PLATFORM_RELATIONSHIPS')) {
	    $relationships = json_decode(base64_decode(getenv('PLATFORM_RELATIONSHIPS')), true);

	    foreach ($relationships['redis_ephemeral'] as $endpoint) {
	    	echo "Configuring Redis Ephemeral on Platform.sh.<br/>";

	        $redis_ephemeral = new Predis\Client(array(
	        	"scheme" => $endpoint['scheme'],
	        	"host" => $endpoint['host'],
	        	"port" => $endpoint['port'])
			);
	    }

	    foreach ($relationships['redis_persistent'] as $endpoint) {
	    	echo "Configuring Redis Persistent on Platform.sh.<br/>";

	        $redis_persistent = new Predis\Client(array(
	        	"scheme" => $endpoint['scheme'],
	        	"host" => $endpoint['host'],
	        	"port" => $endpoint['port'])
			);
	    }
	} else {
		echo "Configuring Redis locally.<br/>";

		$redis = new Predis\Client(array(
	        "scheme" => "tcp",
	        "host" => "127.0.0.1",
	        "port" => 6379)
		);
	}

	$redis_ephemeral->exists("counter1") ? $redis_ephemeral->incr("counter1") : $redis_ephemeral->set("counter1", "1");;
	$redis_persistent->exists("counter2") ? $redis_persistent->incr("counter2") : $redis_persistent->set("counter2", "1");;

	echo "<strong>Redis Ephemeral</strong> says that this page has been viewed " . $redis_ephemeral->get("counter1") . " times!<br/>";
	echo "<strong>Redis Persistent</strong> says that this page has been viewed " . $redis_persistent->get("counter2") . " times!<br/>";
}
catch (Exception $e) {
    echo "Couldn't connect to Redis.";
    echo $e->getMessage();
}

