<?php
    
    define("ENV_PRODUCTION", 'production');
    define("ENV_SANDBOX", 'sandbox');
    
    function getBulkDataExchangeServiceEndpoint($environment)
    {
	    if ( $environment == ENV_PRODUCTION ) {
	        $endpoint = 'https://webservices.ebay.com/BulkDataExchangeService';    
	    }
	    elseif ( $environment == ENV_SANDBOX ) {  
	    	$endpoint = 'https://webservices.sandbox.ebay.com/BulkDataExchangeService';              
	    }
	    else {
	    	die("Invalid Environment: $environment");  
	    }
	    
	    return $endpoint;
    }
    
    function getFileTransferServiceEndpoint($environment)
    {
    	if ( $environment == ENV_PRODUCTION ) {
	        $endpoint = 'https://storage.ebay.com/FileTransferService';    
	    }
	    elseif ( $environment == ENV_SANDBOX ) {  
	    	$endpoint = 'https://storage.sandbox.ebay.com/FileTransferService';              
	    }
	    else {
	    	die("Invalid Environment: $environment");    
	    }
	    
	    return $endpoint;
    }
    
    function getSecurityToken($environment)
    {
	    if ( $environment === ENV_PRODUCTION ) {
	      
            $securityToken = 'AgAAAA**AQAAAA**aAAAAA**nGdBUw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFkoahDpaAqA6dj6x9nY+seQ**FC8CAA**AAMAAA**rbKai4o3qY3uusLJ7MR1MtvQcsY5rz7hmVKDTIfBT0vYaxPb6iJOBmiAU/VdK2TxUYo3eJeBNuBdwfZPE2OeUDNK5eE5YiuNejKHSZbfHJUOErG9qiJL/5GeoqxqUv5yfz1gDgCRVK+o82X6Nrsi6dH6GCW+GnUVuORVPLrhTZjE/b8jYnao0uptul3IFeLo1kTVRvJ2dKqefTy2UMDM2GTrGf0nkEg77+mx08xpDHgmP9jVVMKQDn+qpwP8eNgrMT3qAxUG/6YL9BOHjAHNPAgNtlfqHjN/1wNzIgG2MvNPp035OF+s58LLej2ow/JCRty1FbInb7WaqoacM9I89JAGL2W6p/xhKUJr8vlQNUhIYbL49ZYwx2rGv9ZfkzmfI/tJBKwbHHG1Xg4iUlFn4ys7vPUqN2y3fICNJduKneroiQAx7d5okIqtJtYL7e2onGRdVVnZ6DWtJ+xwiVDoXzFhQLQ48yWBcXCfOylXMMPb6d3H1vstldEpYXMqhmgcNlHZnOVHDOpvFztOA+hnGCn70Tt4fJBYRsu10ImEKzve7NTh1VkrP9Slqc2r30K1xJURtJE4xHSn9luZcGhAYm6IfuNYRfizlSI1LuyTHigRcWazAILHZHtqSXw3pQH/mud5SaLYXnNOgpeAvvcXvkuQUF/iqfcJfGFDDP8IA/ehJIGrr1ZR/5EMKQ9P4iQV+f8uMdJpD781b7Ky/9hKdrzhnjsWAexN7+yhyJkQe32wK9XjpyCdVRPIz7rQFZii';
	    }
	    elseif ( $environment === ENV_SANDBOX ) {  
	        $securityToken = 'AgAAAA**AQAAAA**aAAAAA**yaahUg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GhC5aCpgydj6x9nY+seQ**vG8CAA**AAMAAA**7RXgBfUH76ZQUR1yaq77pXu5DSxzKCxpW0oUntTms0xUalOZPU+U/XptdDAGmj87Tv+N5E3L4w77fCTrz7i8A2FNj/MypwsD1KBMcFHWfEon9RANto+Z9W7n1QaGOBWtJiTlIES0zcZP2oBxD9qShK1wmntupM6soTHNS0G6RReFJZnK4+glvN3DMWpaRk81KaGD0EEwBCb4syJCBGH7xbKnHej1mu4OSXJey7oZaAdsE8ndNtuh+nIMDRO7xfcY2LztXBkv3rQYFRibrNXQDq032/I9A7Ml2hIUzXGYMhJ0PlpkqhVvubDBwsTadL8jRjTmuRkdn1OGcuXnH3svO6ze17MEk8i1q8M8JMtblx2Z2TPxCOJa/7TTV424KbU37+NWw9Rua9sqUClJ/pADlsLJj3tbbaRFWKSC2AWKslzY9rxdbMVWMuIw7zeSNz7sf9gJxfwV8r/WvcIwAKBWVU7rmv7VjKUruN+7Pf5L10zCXweocGi//sLP3NkfNvztGJZN+iKTzqfWT623RZ94UG9DliMxn3XTyrlsI3iM9BlUuD+YphB6IqcpRZAx+A2/Or0coO2/rCe19vXTF5eB6IaJ3k9W7vrSGbT82vImcQmWY6vqlusvMU48UA66oM8e2qKxbmSRsj+Jjw8su0/KjPW+6s+/lpVgTEiwrTwiqz89o22UBhGymVkoaH2cRqJYm7Z5pAFzFV9xACzrHQFdYmNxNTFNvyu8oBR/TUSxKA+5wQaoNRzuNYXD0sA/i9CZ';                 
	    }
	    else {
	    	die("Invalid Environment: $environment");   
	    }
	    
	    return $securityToken;
    }

?>