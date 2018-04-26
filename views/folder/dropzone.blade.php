@section('scripts')

<script>
	Dropzone.autoDiscover = false;
	$('.dz-file-preview').hide();

	jQuery('body').dropzone({ //'#dropzone-uploader'
		previewsContainer: $('.dropzone-preview')[0],
		previewTemplate: $('.dropzone-template').html(),
		clickable: '.dz-clickable',
		url: '{{ route('committees.file.store', $resource_id) }}',
		paramName: 'file', // The name that will be used to transfer the file
	    maxFilesize: 25, // MB
	    parallelUploads: 2, //limits number of files processed to reduce stress on server
	    addRemoveLinks: false,
	    // acceptedFiles: '*/*',
	    accept: function(file, done) {
	        // TODO: Image upload validation
	        done();
	    },
	    drop: function() {
	    	jQuery('#upload-dimmer').dimmer('hide')
	    },
	    dragover: function() {
	    	jQuery('#upload-dimmer').dimmer('show')
	    },
	    dragend: function() {
	    	jQuery('#upload-dimmer').dimmer('hide')
	    },
	    // dragleave: function() {
	    // 	jQuery('#upload-dimmer').dimmer('hide')
	    // },
	    init: function() {
	        this.on('success', function(file, response) {
	            // On successful upload do whatever :-)
	        });
	    },
	    sending: function(file, xhr, formData) {
            // Pass token. You can use the same method to pass any other values as well such as a id to associate the image with for example.
            formData.append('_token', $('input[name=\'_token\']').val() ); // Laravel expect the token post value to be named _token by default
        },
        // addedfile: function(file) {
        // 	// file.previewElement =
        // 	console.log(this.options.previewTemplate)
        // 	console.log(Dropzone.createElement(this.options.previewTemplate));
        // 	$(this.options.previewsContainer).append(file.previewElement);
        //   // file.previewElement.addEventListener("click", function() {
        //   // 	console.log(file);
        //   //   // myDropzone.removeFile(file);
        //   // });
        // },
		// thumbnail: function(file, dataUrl) {
		// 	// Display the image in your file.previewElement
		// },
		// uploadprogress: function(file, progress, bytesSent) {
		// 	// Display the progress
		// },
		success: function (file, response) {
			var $preview = $(file.previewElement);
			var downloadLink = $preview.find('.file-name')[0].dataset.href.replace(/\/file\/1/g, '/file/' + response.id);
			var editLink = $preview.find('.edit-button')[0].dataset.href.replace(/\/file\/1/g, '/file/' + response.id);
			var deleteLink = $preview.find('.delete-button')[0].dataset.href.replace(/\/file\/1/g, '/file/' + response.id);
			$preview.find('.progress').hide();
			$preview.find('.completed').hide();
			$preview.find('.uploading').remove();
			$preview.find('.edit-button').show();
			$preview.find('.file-name').attr('href', downloadLink);
			$preview.find('.edit-button').attr('href', editLink);
			$preview.find('.delete-button').attr('href', deleteLink);
			$preview.find('.delete-button .delete-text').text('Delete');
		},
		error: function (file, response) {
			var $preview = $(file.previewElement);
			$preview.addClass('negative');
			$preview.find('.uploading').remove();
			$preview.find('.progress').hide();

			if(typeof response != 'string' && typeof response.error.exception != 'undefined') {
				response = response.error.exception;
			}

			$preview.find('.dz-error-message').html('<strong>Error Uploading: </strong>');
		},
		// removedfile: function(file) {
		// 	$.ajax({
		// 		data: {
		// 			'_token': $('input[name=\'_token\']').val(),
		// 			'_method': 'DELETE',
		// 		},
		// 		method: 'POST',
		// 		url: '/app/employee/image/' + file.previewElement.dataset.filename,
		// 		success: function() {
		// 			$(file.previewElement).find('input.receipt-image-selector').prop('checked', false);
		// 			$(file.previewElement).remove();
		// 		}
		// 	});
		// }
	});
</script>
@endsection