var LanguageList = function() {
    var initTable = function() {
        var table = $('#languageListTable');

        // begin first table
        datatable = table.DataTable({
            scrollX: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [1, 'desc'],
            ajax: {
                url: '/translations/list',
                type: 'post',
                error: function(response, textStatus, errorThrown) {
                    toastr.error(response.responseJSON.message ?? response.responseJSON.errors);
                }
            },
            columns: [
                { data: 'id', orderable: false, },
                { data: 'language_name' },
                {   data: 'phrases_count',
                    orderable: false,
                    render: function(data) {
                        return data + ' source keys';
                    }
                },
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
                    if(full.language.code ?? 'en' == 'en') {
                        return '-';
                    }
                    var action = `<a class="btn btn-icon view-role" title="View CHA" href=""><i class="la fs-2 text-opacity-75 la-trash"></i></a>`
                    return action;
                }

            }],
        })
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-language-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    }

    $("#languageSelect").select2();

    $("div[language-modal-action='close'], button[language-modal-action='cancel']").on("click", function(e) {
        $("#add_language_form").trigger("reset");
        $("#languageSelect").val(null).trigger("change");
        $("#kt_modal_add_language").modal("hide");
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
            console.log("Form submission triggered");
            
            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                        console.log("Form is valid, proceeding with submission");
                       
                    } else {
                        console.log("Form validation failed");
                    }
                });
            }
        });
    };

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