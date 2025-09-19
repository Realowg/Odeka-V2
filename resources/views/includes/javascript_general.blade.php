<script src="{{ asset('js/core.min.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
    // Load the locale-specific timeago file with fallback to English
    function loadTimeagoLocale(callback) {
        var script = document.createElement('script');
        script.src = "{{ asset('js/jqueryTimeago_'.Lang::locale().'.js') }}";
        script.onload = function() {
            if (callback) callback();
        };
        script.onerror = function() {
            // If locale file fails to load, fall back to English
			console.log('Failed to load timeago library, falling back to English');
            var fallbackScript = document.createElement('script');
            fallbackScript.src = "{{ asset('js/jqueryTimeago_en.js') }}";
            fallbackScript.onload = function() {
                if (callback) callback();
            };
            fallbackScript.onerror = function() {
                console.warn('Failed to load timeago library');
                if (callback) callback();
            };
            document.head.appendChild(fallbackScript);
        };
        document.head.appendChild(script);
    }
    
    // Load timeago first, then load other scripts
    loadTimeagoLocale(function() {
        // Load remaining scripts after timeago is ready
        var scripts = [
            "{{ asset('js/lazysizes.min.js') }}",
            "{{ asset('js/plyr/plyr.min.js') }}?v={{$settings->version}}",
            "{{ asset('js/plyr/plyr.polyfilled.min.js') }}?v={{$settings->version}}",
            "{{ asset('js/app-functions.js') }}?v={{$settings->version}}"
        ];
        
        function loadScript(index) {
            if (index >= scripts.length) return;
            
            var script = document.createElement('script');
            script.src = scripts[index];
            
            // For lazysizes, add async attribute
            if (scripts[index].includes('lazysizes')) {
                script.async = true;
            }
            
            script.onload = function() {
                loadScript(index + 1);
            };
            script.onerror = function() {
                console.warn('Failed to load script:', scripts[index]);
                loadScript(index + 1);
            };
            
            document.head.appendChild(script);
        }
        
        loadScript(0);
    });
</script>

@if (! request()->is('live/*'))
<script src="{{ asset('js/install-app.js') }}?v={{$settings->version}}"></script>
@endif

@auth
  <script src="{{ asset('js/fileuploader/jquery.fileuploader.min.js') }}"></script>
  <script src="{{ asset('js/fileuploader/fileuploader-post.js') }}?v={{$settings->version}}"></script>
  <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
  @if (request()->path() == '/' 
  		&& auth()->user()->verified_id == 'yes' 
		|| request()->route()->named('profile') 
		&& request()->path() == auth()->user()->username  
		&& auth()->user()->verified_id == 'yes'
		)
  <script src="{{ asset('js/jquery-ui/mentions.js') }}"></script>
@endif

@if ($settings->story_status)
<script src="{{ asset('js/story/zuck.min.js') }}?v={{$settings->version}}"></script>
@endif

<script src="https://js.stripe.com/v3/"></script>
<script src='https://checkout.razorpay.com/v1/checkout.js'></script>
<script src='https://js.paystack.co/v1/inline.js'></script>
<script src="https://cdn.kkiapay.me/k.js"></script>
@if (request()->is('my/wallet'))
<script src="{{ asset('js/add-funds.js') }}?v={{$settings->version}}"></script>
@else
<script src="{{ asset('js/payment.js') }}?v={{$settings->version}}"></script>
<script src="{{ asset('js/payments-ppv.js') }}?v={{$settings->version}}"></script>
@endif
<script src="{{ asset('js/send-gift.js') }}?v={{$settings->version}}"></script>
@endauth

@if ($settings->custom_js)
  <script type="text/javascript">
  {!! $settings->custom_js !!}
  </script>
@endif

<script type="text/javascript">
const lightbox = GLightbox({
    touchNavigation: true,
    loop: false,
    closeEffect: 'fade'
});

@auth
$('.btnMultipleUpload').on('click', function() {
  $('.fileuploader').toggleClass('d-block');
});

	@if (request()->route()->named('post.edit') && $preloadedFile)
	$(document).ready(function() {
		$('.fileuploader').addClass('d-block');
	});
	@endif

@endauth
</script>

@if (auth()->guest()
    && ! request()->is('password/reset')
    && ! request()->is('password/reset/*')
    && ! request()->is('contact')
    )
<script type="text/javascript">
	//<---------------- Login Register ----------->>>>
	onSubmitformLoginRegister = function() {
		  sendFormLoginRegister();
		}

	if (! captcha) {
	    $(document).on('click','#btnLoginRegister',function(s) {
 		 s.preventDefault();
		 sendFormLoginRegister();
 	 });//<<<-------- * END FUNCTION CLICK * ---->>>>
	}

	function sendFormLoginRegister() {
		var element = $(this);
		$('#btnLoginRegister').attr({'disabled' : 'true'});
		$('#btnLoginRegister').find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

		(function(){
			 $("#formLoginRegister").ajaxForm({
			 dataType : 'json',
			 success:  function(result) {

         if (result.actionRequired) {
           $('#modal2fa').modal({
    				    backdrop: 'static',
    				    keyboard: false,
    						show: true
    				});

            $('#loginFormModal').modal('hide');
           return false;
         }

				 // Success
				 if (result.success) {

           if (result.isModal && result.isLoginRegister) {
             window.location.reload();
           }

					 if (result.url_return && ! result.isModal) {
					 	window.location.href = result.url_return;
					 }

					 if (result.check_account) {
					 	$('#checkAccount').html(result.check_account).fadeIn(500);

						$('#btnLoginRegister').removeAttr('disabled');
						$('#btnLoginRegister').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						$('#errorLogin').fadeOut(100);
						$("#formLoginRegister").reset();
					 }

				 }  else {

					 if (result.errors) {
						 var error = '';
						 var $key = '';

					for ($key in result.errors) {
							 error += '<li><i class="far fa-times-circle"></i> ' + result.errors[$key] + '</li>';
						 }

						 $('#showErrorsLogin').html(error);
						 $('#errorLogin').fadeIn(500);
						 $('#btnLoginRegister').removeAttr('disabled');
						 $('#btnLoginRegister').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');

						 if (captcha) {
							grecaptcha.reset();
						 }
					 }
				 }
				},

				statusCode: {
						419: function() {
							window.location.reload();
						}
					},
				error: function(responseText, statusText, xhr, $form) {
						// error
						$('#btnLoginRegister').removeAttr('disabled');
						$('#btnLoginRegister').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						swal({
								type: 'error',
								title: error_oops,
								text: error_occurred+' ('+xhr+')',
							});
							
						if (captcha) {
							grecaptcha.reset();
						 }
				}
			}).submit();
		})(); //<--- FUNCTION %
	}
</script>
@endif
