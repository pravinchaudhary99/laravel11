var LanguageList = function() {
    var initTable = function() {
        var table = $('#translationListTable');
        var id = table.attr('data-id');
        // begin first table
        datatable = table.DataTable({
            scrollX: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [1, 'asc'],
            ajax: {
                url: '/translations/phrases/list/'+id,
                type: 'post',
                data: function(d) {
                    d.file = $('select[data-kt-translate-table-filter="file"]').val();
                    d.status = $('select[data-kt-translate-table-filter="status"]').val();
                },
                error: function(response, textStatus, errorThrown) {
                    toastr.error(response.responseJSON.message ?? response.responseJSON.errors);
                }
            },
            columns: [
                { data: 'id', orderable: false, },
                { data: 'key' },
                { data: 'value' },
                { data: 'id'},
            ],
            columnDefs: [{
                targets: 0,
                orderable: false,
                render: function(data, type, full, meta) {
                    let start = datatable.ajax.params().start;
                    return start + meta.row + 1;
                },
            },{
                targets: -1,
                title: 'Actions',
                orderable: false,
                className: 'text-center',
                render: function(data, type, full, meta) {
                    var action = `
                    <a href="javascript:void(0);" class="btn btn-icon editTranslationButton" title="edit translation" data-id="${data}" data-key="${full.key}" data-value="${full.value ?? ''}"><i class="la fs-2 text-opacity-75 la-edit"></i></a>`;
                    return action;
                }

            }],
        })
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-translate-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });

        $(document).on('change', 'select[data-kt-translate-table-filter="file"], select[data-kt-translate-table-filter="status"]', function() {
            datatable.ajax.reload();
        });
    }

    $("#languageSelect").select2();

    $("div[language-modal-action='close'], button[language-modal-action='cancel']").on("click", function(e) {
        $("#add_language_form").trigger("reset");
        $("#languageSelect").val(null).trigger("change");
        $("#kt_modal_add_language").modal("hide");
    });

    $(document).on('click', 'a.deleteTranslation', function() {
        var deleteId = $(this).attr('data-id');

        Swal.fire({
            text: "Are you sure you want to delete translation?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            showCloseButton: true,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-light"
            }
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    method: "DELETE",
                    url: '/translations/destroy/' + deleteId,
                    success: function(response) {
                        toastr.success(response.message ?? 'Translation has been deleted successfully');
                        datatable.ajax.reload(null, false);
                    },
                    error: function(response) {
                        toastr.error(response.responseJSON.error ?? 'something went wrong');
                    }
                });
            }
        })
    });


    $(document).on('click', '.editTranslationButton', function(e) {
        e.preventDefault();
        var key = $(this).attr('data-key');
        var value = $(this).attr('data-value') ?? '';
        var id = $(this).attr('data-id');

        $("#update_translation_value").modal("show");
        $(document).find("#englishValue").text(key);
        $(document).find("#translationValue").val(value);
        $(document).find("#translationFormSubmit").attr('data-id', id);
    });

    var handleForm = function() {
        // Initialize form validation
        var formElement = document.getElementById('add_language_form');
        var validator = FormValidation.formValidation(
            formElement, {
                fields: {
                    'language': {
                        validators: {
                            notEmpty: {
                                message: 'Language name is required' // Updated message
                            },
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );
    
        document.getElementById("languageFormSubmit").addEventListener("click", function(e) {
            e.preventDefault();
            var language = $("select[name=language] option:selected").val();
            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                       $.ajax({
                            method: "POST",
                            url: "/translations/store",
                            data: { "language" : language},
                            success: function(response) {
                                if(response.success) {
                                    toastr.success(response.message ?? 'CHA created successfully')
                                }
                                $("#kt_modal_add_language").modal('hide');
                                datatable.ajax.reload(null, false);
                            },
                            error: function(response) {
                                const errorMessage = getErrorMessage(response);
                                toastr.error(errorMessage);
                            }
                       });
                       
                    } else {
                        console.log("Form validation failed");
                    }
                });
            }
        });
    };

    function getErrorMessage(response) {
        if (!response || !response.responseJSON) {
            return 'Something went wrong';
        }
    
        const { errors, message } = response.responseJSON;
    
        if (errors && errors.length > 0) {
            return errors;
        }
    
        if (message) {
            return message;
        }
    
        return 'Something went wrong';
    }

    return {
        init: function() {
            initTable();
            handleSearchDatatable();
            handleForm();
        }
    }
}();

KTUtil.onDOMContentLoaded((function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    LanguageList.init()
}));