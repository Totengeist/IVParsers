<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Uppy</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://releases.transloadit.com/uppy/v3.3.1/uppy.min.css" rel="stylesheet">
    <style>
        #ship-info, #drag-drop-area, #drag-drop-area.uppy-Dashboard-inner {
            padding-top: 25px;
            padding-bottom: 25px;
            margin: auto;
            width: 65%;
        }
        .ship-name {
            text-transform: uppercase;
            text-align: center;
            border: 0px !important;
        }
        #ship-info {
        }
    </style>
  </head>
  <body>
    <div class="container">
        <h1>Ship Statistics</h1>
        <p>To view statistics about your ship, upload it using the field below. To request features
        or report bugs, please contact Totengeist on the <a href="https://discord.gg/U9GdkprZKW">TLS
        Discord</a>.</p>
        <p>Uploaded files are automatically deleted after processing. You can reuse the upload form
        to analyze one ship after another. Analyzing multiple ships at once is not yet supported.</p>
        <p><b>Note:</b> If your ship's name shows as "Empty", you can change this by opening the
        file in any editor and updating the "Name" field towards the top of the file.</p>
        <table id="ship-info" class="table"></table>
        <div id="drag-drop-area"></div>
    </div>

    <script type="module">
      import {Uppy, Dashboard, XHRUpload, Url} from "https://releases.transloadit.com/uppy/v3.3.1/uppy.min.mjs"
      var uppy = new Uppy({restrictions: {
        maxFileSize: 3000000,
        maxNumberOfFiles: 1,
        allowMultipleUploads: false,
      }})
        .use(Dashboard, {
          inline: true,
          target: '#drag-drop-area'
        })
        .use(XHRUpload, {endpoint:'file.php', method:'post'});
        
        uppy.on('upload-success', function (file, data) {
            console.log(data.body);
            var content = "<tr><th colspan=2 class='ship-name'>" + data.body["Name"] + "</th></tr>"+
                          "<tr><th>Type</th><td>" + data.body["Type"] + "</td></tr>";
            delete data.body["Name"];
            delete data.body["Type"];
            for (var key in data.body) {
                if (data.body.hasOwnProperty(key)) {
                    content += "<tr><th>" + key + "</th><td>" + data.body[key] + "</td></tr>";
                    console.log(key + " -> " + data.body[key]);
                }
            }
            document.getElementById("ship-info").innerHTML = content;
            console.log("Uploaded!");
            uppy.removeFile(file.id);
        });
        uppy.on('upload-error', (file, error, response) => {
            alert("Failed to upload " + file.name + ": " + response.body.error);
        });
    </script>
  </body>
</html>