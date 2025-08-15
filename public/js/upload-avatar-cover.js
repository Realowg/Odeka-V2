//<--------- Upload Avatar and Cover -------//>
(function ($) {
  "use strict";

  //<<<<<<<=================== * UPLOAD AVATAR  * ===============>>>>>>>//
  // Handle avatar upload button click
  $(document).on('click', '#avatar_file', function(e) {
    e.preventDefault();
    $('#uploadAvatar').trigger('click');
  });

  $(document).on('change', '#uploadAvatar', function () {
    var file = this.files[0];
    
    // Validate file before upload
    if (!file) {
      return false;
    }

    // Check file type
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
      swal({
        title: error_oops || 'Error',
        text: 'Please select a valid image file (JPG, PNG, GIF)',
        type: "error",
        confirmButtonText: ok || 'OK'
      });
      $(this).val('');
      return false;
    }

    // Check file size (if defined)
    if (typeof max_file_size !== 'undefined' && file.size > max_file_size) {
      swal({
        title: error_oops || 'Error',
        text: 'File size is too large',
        type: "error",
        confirmButtonText: ok || 'OK'
      });
      $(this).val('');
      return false;
    }

    $('.progress-upload').show();

    (function () {
      var percent = $('.progress-upload');
      var percentVal = '0%';

      $("#formAvatar").ajaxForm({
        dataType: 'json',
        timeout: 60000, // 60 seconds timeout
        
        error: function (xhr, status, error) {
          var errorMessage = error_occurred || 'An error occurred';
          
          if (xhr.status === 413) {
            errorMessage = 'File is too large';
          } else if (xhr.status === 422) {
            try {
              var response = JSON.parse(xhr.responseText);
              if (response.errors) {
                errorMessage = Object.values(response.errors).flat().join('<br>');
              }
            } catch (e) {
              errorMessage = 'Validation error occurred';
            }
          }

          $('.popout').removeClass('popout-success').addClass('popout-error')
            .css('background-color', '#dc3545')
            .html(errorMessage).fadeIn('500').delay('5000').fadeOut('500');
          $('.progress-upload').hide();
          $('#uploadAvatar').val('');
          percent.html('0%');
        },

        beforeSend: function () {
          percent.html('0%');
        },
        
        uploadProgress: function (event, position, total, percentComplete) {
          var percentVal = percentComplete + '%';
          percent.html(percentVal);
        },
        
        success: function (response) {
          if (response && response.success === false) {
            $('.progress-upload').hide();

            var error = '';
            if (response.errors) {
              for (var key in response.errors) {
                if (Array.isArray(response.errors[key])) {
                  error += response.errors[key].join('<br>') + '<br>';
                } else {
                  error += response.errors[key] + '<br>';
                }
              }
            }

            swal({
              title: error_oops || 'Error',
              text: error || 'Upload failed',
              type: "error",
              confirmButtonText: ok || 'OK'
            });

            $('#uploadAvatar').val('');
            percent.html('0%');

          } else if (response && response.success === true) {
            $('#uploadAvatar').val('');
            $('.avatarUser').attr('src', response.avatar);
            $('.progress-upload').hide();
            percent.html('0%');

            // Show success message if available
            if (response.message) {
              $('.popout').removeClass('popout-error').addClass('popout-success')
                .css('background-color', '#28a745')
                .html(response.message).fadeIn('500').delay('3000').fadeOut('500');
            }
          } else {
            $('.progress-upload').hide();
            percent.html('0%');
            swal({
              title: error_oops || 'Error',
              text: error_occurred || 'An unexpected error occurred',
              type: "error",
              confirmButtonText: ok || 'OK'
            });
            $('#uploadAvatar').val('');
          }
        }
      }).submit();
    })();
  });
  //<<<<<<<=================== * END UPLOAD AVATAR  * ===============>>>>>>>//

  //<<<<<<<=================== * UPLOAD COVER  * ===============>>>>>>>//
  $(document).on('change', '#uploadCover', function () {


    $('#coverFile').attr({ 'disabled': 'true' }).html('<i class="spinner-border spinner-border-sm"></i>');

    $('.progress-upload-cover').show();

    (function () {

      var percent = $('.progress-upload-cover');
      var percentVal = '0%';

      $("#formCover").ajaxForm({
        dataType: 'json',
        error: function (responseText, statusText, xhr, $form) {

          $('.popout').removeClass('popout-success').addClass('popout-error').html(error_occurred + ' ' + xhr + '').fadeIn('500').delay('5000').fadeOut('500');
          $('#coverFile').removeAttr('disabled').html('<i class="fa fa-camera mr-1"></i> <span class="d-none d-lg-inline">' + change_cover + '</span>');
          $('.progress-upload-cover').hide();
          $('#uploadCover').val('');
          percent.width(percentVal);
        },

        beforeSend: function () {
          percent.width(percentVal);
        },
        uploadProgress: function (event, position, total, percentComplete) {
          var percentVal = percentComplete + '%';
          percent.width(percentVal);
        },
        success: function (e) {
          if (e) {

            if (e.success == false) {

              $('#coverFile').removeAttr('disabled').html('<i class="fa fa-camera mr-1"></i> <span class="d-none d-lg-inline">' + change_cover + '</span>');
              $('.progress-upload-cover').hide();

              var error = '';
              var $key = '';

              for ($key in e.errors) {
                error += '' + e.errors[$key] + '';
              }
              swal({
                title: error_oops,
                text: "" + error + "",
                type: "error",
                confirmButtonText: ok
              });

              $('#uploadCover').val('');
              percent.width(percentVal);

            } else {

              $('#coverFile').removeAttr('disabled').html('<i class="fa fa-camera mr-1"></i> <span class="d-none d-lg-inline">' + change_cover + '</span>');
              $('#uploadCover').val('');
              $('.jumbotron-cover-user').css({ padding: '240px 0', background: 'url("' + e.cover + '") center center #505050', backgroundSize: 'cover', transition: 'padding .6s ease' });
              $('.progress-upload-cover').hide();
              percent.width(percentVal);
            }

          }//<-- e
          else {
            $('#coverFile').removeAttr('disabled').html('<i class="fa fa-camera mr-1"></i> <span class="d-none d-lg-inline">' + change_cover + '</span>');
            $('.progress-upload-cover').hide();
            percent.width(percentVal);
            swal({
              title: error_oops,
              text: error_occurred,
              type: "error",
              confirmButtonText: ok
            });

            $('#uploadCover').val('');
          }
        }//<----- SUCCESS
      }).submit();
    })(); //<--- FUNCTION %
  });//<<<<<<<--- * ON * --->>>>>>>>>>>
  //<<<<<<<=================== * END UPLOAD COVER  * ===============>>>>>>>//

})(jQuery);
