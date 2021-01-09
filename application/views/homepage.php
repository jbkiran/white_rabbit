<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<title>Directory Listing</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	</head>
	<body>

		<div class="container">
		<h4>Folder listing of :- <span><i id="directoryName"></i></span></h4>    
		<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#uploadModal">upload file</button>    
		<table class="table table-striped">
			<thead>
			<tr>
				<th>sl.no</th>
				<th>Filename</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody id="listings">
			
			</tbody>
		</table>
		</div>

		<!-- Modal used to upload file -->
		<div id="uploadModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Upload your file</h4>
					</div>
					<div class="modal-body">
					<form id="data" method="post" enctype="multipart/form-data">
						<input name="file" type="file" />
					</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-success" id="success_upload" >Upload</button>
					</div>
				</div>

			</div>
		</div>

	</body>

	<script>
		var __filesList   		= "<?php echo $data; ?>";
		var __status			= "<?php echo $status; ?>";	
		var __message			= "<?php echo $message; ?>";
		var __directory 		= "<?php echo $directory; ?>";
		var __offset			= "<?php echo $offset; ?>";
		var __limit				= "<?php echo $limit; ?>";
		var __totalRecords		= "<?php echo $total; ?>";

		$(document).ready(function(){
			
			$('#directoryName').text(__directory);

			if(__status == true){
				$('#listings').html(renderData($.parseJSON(__filesList)));
			}else{
				$('#listings').html(__message);
			}
			
		});
		function nextPage(){

			if (__offset > __totalRecords) {
   				__offset = __totalRecords;
			}
			if (__offset < 1) {
				__offset = 1;
			} 

			$pageOffset = (__offset - 1) * __limit;

			$.ajax({
                url: "<?php echo base_url('Homepage/list_directory'); ?>",
                type: "post",
                data: {
                    offset		: pageOffset
                },
                success: function (result) {
                    
					var response = $.parseJSON(result);
					if(response.status == true){
						$('#listings').html(renderData(response.data));
					}else{
						$('#listings').html(response.message);
					}
                }
            });
		}

		//render list
		function renderData(fileList){

			var iteration 		= 1;
			var renderResponse 	= '';
			$.each(fileList, function(filesKey,filesName)
			{
				var listingParams 		= {};
				listingParams.listCount = iteration;
				listingParams.listName	= filesName;
				renderResponse 		   += renderList(listingParams);
				iteration++;
			});
			return renderResponse;
		}
		// render row level
		function renderList(params){

			var listingFiles = `<tr>;
				<td>${params.listCount}</td>
				<td>${params.listName}</td>
				<td><button type="button" class="btn btn-danger removeFiles" data-id="${params.listName}">&#10060;</button></td>
			</tr>`;
			return listingFiles;
		}
		//remove files from server directory
		$(document).on('click','.removeFiles',function(){

			var fileName = $(this).attr('data-id');
			$.ajax({
                url: "<?php echo base_url('Homepage/remove_file'); ?>",
                type: "post",
                data: {
                    file_name: fileName,
                },
                success: function (result) {
                    
					
                }
            });
		});

		$(document).on('click','#success_upload',function(e){

			e.preventDefault();    
			var formData = new FormData(this);

			$.ajax({
				url: "<?php echo base_url('Homepage/upload_file'); ?>",
				type: 'POST',
				data: formData,
				success: function (data) {
					
				},
				cache: false,
				contentType: false,
				processData: false
			});
		});

	</script>
</html>
