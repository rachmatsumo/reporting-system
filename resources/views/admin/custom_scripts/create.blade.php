@extends('layouts.user_type.auth')
@section('title', 'Tambah Custom Script')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<style>
    .CodeMirror {
        height: 400px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .CodeMirror-gutters {
        background-color: #2f3129;
        border-right: 1px solid #3c3c3c;
    }
    .CodeMirror-linenumber {
        color: #75715e;
        padding: 0 8px 0 0;
    }
</style>
@endpush

@section('content')
<div class="container">
    <x-page-header route-prefix="custom-scripts" mode="create" />
      
    <form method="POST" action="{{ route('custom-scripts.store') }}">
        @csrf 

        <div class="row mb-3">
            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Script Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name') }}" required> 
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div> 

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select type="is_active" name="is_active" class="form-select @error('is_active') is-invalid @enderror" required> 
                    <option value="" disabled selected>Select Status</option>
                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Active</option> 
                </select>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> 

            <div class="col-12 col-md-12 mb-3">
                <label class="form-label">Script <span class="text-danger">*</span></label>
                <textarea rows="20" id="script" name="script" class="form-control @error('script') is-invalid @enderror">{{ old('script') }}</textarea>
                @error('script')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> 

        </div>  

        <x-form-buttons route-prefix="custom-scripts" mode="create" />
    </form>
                       
</div>  

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/selection/active-line.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>

<script>
    $(document).ready(function() {
        let editor;
        
        // Initialize CodeMirror
        editor = CodeMirror.fromTextArea(document.getElementById('script'), {
            mode: 'javascript',
            theme: 'monokai',
            lineNumbers: true,
            lineWrapping: true,
            matchBrackets: true,
            autoCloseBrackets: true,
            styleActiveLine: true,
            indentUnit: 4,
            tabSize: 4
        });
        
        editor.setSize("100%", "400px");
        
        // Custom form validation
        $('#scriptForm').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            $('.invalid-feedback').hide().text('');
            $('.form-control').removeClass('is-invalid');
            
            let isValid = true;
            
            // Validate script name
            const scriptName = $('#script_name').val().trim();
            if (!scriptName) {
                $('#script_name').addClass('is-invalid');
                $('#script_name_error').text('Script name is required.').show();
                isValid = false;
            }
            
            // Validate status
            const status = $('#status').val();
            if (!status) {
                $('#status').addClass('is-invalid');
                $('#status_error').text('Status is required.').show();
                isValid = false;
            }
            
            // Validate script content
            editor.save(); // Update textarea with CodeMirror content
            const scriptContent = editor.getValue().trim();
            if (!scriptContent) {
                // Add visual indication to CodeMirror
                $('.CodeMirror').addClass('is-invalid');
                $('#script_error').text('Script content is required.').show();
                isValid = false;
            } else {
                $('.CodeMirror').removeClass('is-invalid');
            }
            
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields.'
                });
                
                // Focus on first error field
                if (!scriptName) {
                    $('#script_name').focus();
                } else if (!status) {
                    $('#status').focus();
                } else if (!scriptContent) {
                    editor.focus();
                }
                
                return false;
            }
            
            // If validation passes, submit form
            submitForm();
        });
        
        function submitForm() {
            const $btn = $('#saveBtn');
            
            // Disable button and show loading
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            const formData = {
                script_name: $('#script_name').val(),
                status: $('#status').val(),
                script: editor.getValue(),
                _token: $('input[name="_token"]').val()
            };
            
            $.ajax({
                url: "{{ route('custom-scripts.store') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Script saved successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('custom-scripts.index') }}";
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while saving.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors from server
                        const errors = xhr.responseJSON.errors;
                        
                        Object.keys(errors).forEach(field => {
                            const errorElement = $(`#${field}_error`);
                            const inputElement = $(`#${field}`);
                            
                            if (errorElement.length) {
                                errorElement.text(errors[field][0]).show();
                                inputElement.addClass('is-invalid');
                            }
                        });
                        
                        errorMessage = 'Please check the form for errors.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete: function() {
                    // Re-enable button
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Script');
                }
            });
        }
    });
</script>
@endsection