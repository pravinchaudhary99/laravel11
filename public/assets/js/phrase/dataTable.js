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
                { data: 'uuid'},
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
                    <a href="javascript:void(0);" class="btn btn-icon editTranslationButton" title="edit translation" data-id="${data}" data-key="${full.enValue}" data-value="${full.value ?? ''}"><i class="la fs-2 text-opacity-75 la-edit"></i></a>`;
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


    $("div[translation-modal-action='close'], button[translation-modal-action='cancel']").on("click", function(e) {
        $("#update_translation_value_form").trigger("reset");
        $("#update_translation_value").modal("hide");
    });

    $(document).on('click', '.editTranslationButton', function(e) {
        e.preventDefault();
        const key = $(this).attr('data-key');
        const value = $(this).attr('data-value') ?? '';
        const id = $(this).attr('data-id');

        const modal = $("#update_translation_value");
        modal.find("#englishValue").text(key);
        modal.find("#translationValue").val(value);
        modal.find("#translationFormSubmit").attr('data-id', id)
        
        $.ajax({
            method: 'POST',
            url: '/translations/phrases/translate/'+id,
            data: { 'key': key},
            success: function(response) {
                modal.find(".translatedValue").text(response.translated || ''); 
                modal.modal('show');
            }
        });
    });

    $(document).on('click', '#copyButton', function() {
        var textToCopy = $(document).find(".translatedValue").text();
        
        if (textToCopy) {
            navigator.clipboard.writeText(textToCopy);
            $(document).find("#translationValue").val(textToCopy)
        } else {
            console.log('No text to copy.');
        }
    });

    var handleForm = function() {
        // Initialize form validation
        var formElement = document.getElementById('update_translation_value_form');
        var validator = FormValidation.formValidation(
            formElement, {
                fields: {
                    'value': {
                        validators: {
                            notEmpty: {
                                message: 'Translate value is required'
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
    
        document.getElementById("translationFormSubmit").addEventListener("click", function(e) {
            e.preventDefault();
            var value = $("#translationValue").val();
            var id = $("#translationFormSubmit").attr("data-id");
            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                       $.ajax({
                            method: "POST",
                            url: "/translations/phrases/update/"+id,
                            data: { "value" : value},
                            success: function(response) {
                                if(response.success) {
                                    toastr.success(response.message ?? 'CHA created successfully')
                                }
                                $("#update_translation_value").modal('hide');
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

    $("div[language-modal-action='close'], button[language-modal-action='cancel']").on("click", function(e) {
        $("#update_translation_value_form").trigger("reset");
        $("#update_translation_value").modal("hide");
    });

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