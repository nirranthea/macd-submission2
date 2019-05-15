<html>
 <head>
 <Title>Image Analysis</Title>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
 <style type="text/css">
 	body { background-color: #fff; border-top: solid 10px #000;
 	    color: #333; font-size: .85em; margin: 20; padding: 20;
 	    font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
 	}
 	h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
 	h1 { font-size: 2em; }
 	h2 { font-size: 1.75em; }
 	h3 { font-size: 1.2em; }
 	table { margin-top: 0.75em; }
 	th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
 	td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
 </style>
 </head>
 <body>
 <p>Browse file, lalu click tombol <strong>Upload</strong> untuk mengupload image ke Azure.</p>
 <form method="post" action="index.php" enctype="multipart/form-data" >
       <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
       <input type="submit" name="submit" value="Upload">
 </form>
 
 <?php
    require_once 'vendor/autoload.php';
	require_once "./random_string.php";

	use MicrosoftAzure\Storage\Blob\BlobRestProxy;
	use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
	use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
	use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
	use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
	
	// $connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=nirrantheastorageapp;AccountKey=aLLbrBRlXUZDD45Un452XjJsiz4DFAQ4aA4CXk04ax77lfihVed/VhZNBQd+A9cQYqsyWfiEpjoEY04NrSQxcA==";
	$blobClient = BlobRestProxy::createBlobService($connectionString);
	$createContainerOptions = new CreateContainerOptions();
	$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
	$createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
	$containerName = "nirrantheablockblobs";
	try{
		// Create container.
        $blobClient->createContainer($containerName, $createContainerOptions);
		
		
	}
	catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        // echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        // echo $code.": ".$error_message."<br />";
    }
	
	
	if (isset($_POST['submit'])) {
		$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
		$content = fopen($_FILES["fileToUpload"]["tmp_name"],"r");
		
		// echo "Uploading BlockBlob: ".PHP_EOL;
		// echo $fileToUpload;
		// echo "<br />";
		
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
		
	}
	
	
	//Load Data
	// List blobs.
	$listBlobsOptions = new ListBlobsOptions();
	
	$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
	if (count($result->getBlobs()) > 0) {
		echo "<h2>Gambar yang telah diupload di Blobs :</h2>";
		echo "<table>";
                echo "<tr><th>Nama File</th>";
                echo "<th>Url</th></tr>";
                foreach($result->getBlobs() as $blob) {
                    echo "<tr><td>".$blob->getName()."</td>";
                    echo "<td>".$blob->getUrl()."</td></tr>";
                }
                echo "</table>";
	}
	else {
		echo "<h2>Belum ada Gambar yang diupload di Blobs :</h2>";
	}
 ?>
 
  <script type="text/javascript">
        function processImage() {
            // **********************************************
            // *** Update or verify the following values. ***
            // **********************************************
     
            // Replace <Subscription Key> with your valid subscription key.
            var subscriptionKey = "af1bca8ec2414804a053d88249f3bb0a	";
     
            // You must use the same Azure region in your REST API method as you used to
            // get your subscription keys. For example, if you got your subscription keys
            // from the West US region, replace "westcentralus" in the URL
            // below with "westus".
            //
            // Free trial subscription keys are generated in the "westus" region.
            // If you use a free trial subscription key, you shouldn't need to change
            // this region.
            var uriBase =
                "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
				//"https://southeastasia.api.cognitive.microsoft.com/";
     
            // Request parameters.
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
     
            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;
     
            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
     
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
     
                type: "POST",
     
                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })
     
            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
            })
     
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };
    </script>
     
    <h1>Analisa Gambar</h1>
    Masukkan URL salah satu gambar di atas, lalu click tombol <strong>Analyze image</strong>.
    <br><br>
    Gambar yang dianalisa:
    <input type="text" name="inputImage" id="inputImage"
        value="url" />
    <button onclick="processImage()">Analyze image</button>
    <br><br>
    <div id="wrapper" style="width:1020px; display:table;">
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Response:
            <br><br>
            <textarea id="responseTextArea" class="UIInput"
                      style="width:580px; height:400px;"></textarea>
        </div>
        <div id="imageDiv" style="width:420px; display:table-cell;">
            Source image:
            <br><br>
            <img id="sourceImage" width="400" />
        </div>
    </div>
 </body>
 </html>