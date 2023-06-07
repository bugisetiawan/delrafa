$(document).ready(function() {

    $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
        return {
            "iStart": oSettings._iDisplayStart,
            "iEnd": oSettings.fnDisplayEnd(),
            "iLength": oSettings._iDisplayLength,
            "iTotal": oSettings.fnRecordsTotal(),
            "iFilteredTotal": oSettings.fnRecordsDisplay(),
            "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
            "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
        };
    };

    var table = $("#tables").DataTable({
        responsive: true,
        processing: true,
        language: {            
            'processing': '<button class="btn btn-lg btn-outline-dark kt-spinner kt-spinner--lg kt-spinner--primary">Loading...</button>'
        },
        serverSide: true,
        pageLength: 25,
        lengthMenu: [ [25, 50, 75, -1], [25, 50, 75, "All"] ],
        ajax: {
            "url": "master/Department/datatable_department",
            "type": "POST"
        },
        columns: [{
                "data": "d_id",
                "className": "kt-font-dark text-center"
            },
            {
                "data": "d_code",
                "className": "kt-font-dark text-center"
            },
            {
                "data": "d_name",
                "className": "kt-font-dark"
            },
            {
                "data": "s_code",
                "className": "kt-font-dark text-center"
            },
            {
                "data": "s_name",
                "className": "kt-font-dark"
            },
            {
                "data": "view",
                "className": "kt-font-dark text-center"
            }
        ],
        order: [
            [1, 'asc'],
            [3, 'asc']
        ],
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });

    // UPPERCASE NAME
    $('#name, #department_update, #subdepartment_name').keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    $("#create_data").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "master/department/add_process",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: (data) => {
                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                if(data.status.code == 200) {
                    $("#form_add").modal("hide");
                    $("input, select").val("");
                    toastr.success(data.status.message, "Berhasil");
                    table.ajax.reload();
                    $(".code").hide();
                }
            },
            error: (err) => {
                toastr.error(err.responseText, 'Gagal');
            }
        });
    });

    $.getJSON("master/department/get_department", (data) => {
        if (data.status.code == 200) {
            var option = '<option value="">- PILIH KATEGORI -</option>';
            $.each(data.response, function(i, item) {
                option += '<option value="' + data.response[i].code + '">' + data.response[i].name + '</option>';
            });
            $("#code, #department_code").html(option);
        } else {
            // alert(data.status.message);
        }
    });

    $(".code").hide();
    $("#type").change(function() {
        var type = $(this).val();
        if (type == '2') {
            $(".code").show();
            $.getJSON("master/department/get_department", (data) => {
                if (data.status.code == 200) {
                    var option = '<option value="">- PILIH KATEGORI -</option>';
                    $.each(data.response, function(i, item) {
                        option += '<option value="' + data.response[i].code + '">' + data.response[i].name + '</option>';
                    });
                    $("#code, #department_code").html(option);
                    $("#keterangan").html('SUBKATEGORI');
                } else {
                    // alert(data.status.message);
                }
            });
        } else {
            $(".code").hide();
            $("#keterangan").html('NAMA');
        }
    });

    // DETAIL DATA
    $("#table_data").on("click", "#update_department", function() {
        let department_code = $(this).data("code");
        $.ajax({
            url: "master/department/check_code_department",
            type: "GET",
            dataType: "JSON",
            data: {
                department_code: department_code
            },
            success: (data) => {
                if (data.status.code != 200) {
                    toastr.error(data.status.message);
                } else {
                    $.ajax({
                        url: "master/department/detail_department",
                        type: "GET",
                        dataType: "JSON",
                        data: {
                            department_code: department_code
                        },
                        success: (data) => {
                            if (data.status.code == 200) {
                                $("#form_update").modal("show");
                                $("#code_update").val(data.response.code);
                                $("#department_update").val(data.response.name);
                            } else {
                                toastr.error(data.status.message);
                            }
                        },
                        error: (err) => {

                        }
                    });
                }
            },
            error: (err) => {

            }
        });
    });
    $("#table_data").on("click", "#update_subdepartment", function() {
        let subdepartment_id = $(this).data("id");
        $.ajax({
            url: "master/department/check_id_subdepartment",
            type: "GET",
            dataType: "JSON",
            data: {
                subdepartment_id: subdepartment_id
            },
            success: (data) => {
                if (data.status.code != 200) {
                    toastr.error(data.status.message);
                } 
                else 
                {                    
                    $("#modal_updatesub").modal("show");                    
                    $("#subdepartment_id").val(data.response.id);
                    $("#department_code").val(data.response.department_code);
                    $("#department_code_old").val(data.response.department_code);
                    $("#subdepartment_name").val(data.response.name);
                }
            },
            error: (err) => {
                console.log(err);
            }
        });
    });
    // DETAIL DATA

    /**
     * UPDATE DATA
     */
    $("#update_department").on("submit", function(e) {
        e.preventDefault();
        let t = $(this);
        $.ajax({
            url: t.attr('action'),
            type: t.attr('method'),
            dataType: "JSON",
            data: t.serialize(),
            success: (data) => {
                if (data.status.code == 200) {
                    $("#form_update").modal("hide");
                    $("input, select").val("");
                    toastr.success(data.status.message);
                    table.ajax.reload();
                } else {
                    toastr.error(data.status.message);
                }
            },
            error: (err) => {
                toastr.error(err.status.message);
            }
        });
    });

    $("#form_updatesub").on("submit", function(e) {
        e.preventDefault();
        let t = $(this);
        $.ajax({
            url: t.attr('action'),
            type: t.attr('method'),
            dataType: "JSON",
            data: t.serialize(),
            success: (data) => {
                if (data.status.code == 200) {
                    $("#modal_updatesub").modal("hide");
                    $("input, select").val("");
                    toastr.success(data.status.message);
                    table.ajax.reload();
                } else {
                    toastr.error(data.status.message);
                }
            },
            error: (err) => {

            }
        });
    });

    /**
     * DELETE DATA
     */
    $("#table_data").on("click", "#delete_department", function() {
        let department_code = $(this).data("code");
        console.log(department_code);
        $.ajax({
            url: "master/department/check_code_department",
            type: "GET",
            dataType: "JSON",
            data: {
                department_code: department_code
            },
            success: (data) => {
                console.log(data);
                if (data.status.code != 200) {
                    toastr.error(data.status.message)
                } else {
                    swal.fire({
                        title: 'Hapus Data?',
                        text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                        reverseButtons: true
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                url: "master/department/delete_department_process",
                                method: "POST",
                                dataType: "JSON",
                                data: {
                                    department_code: department_code
                                },
                                success: (data) => {
                                    if (data.status.code == 200) {
                                        toastr.success(data.status.message);
                                        table.ajax.reload();
                                    } else {
                                        toastr.error(data.status.message);
                                    }
                                },
                                error: (err) => {
                                    toastr.error(err.responseText);
                                }
                            })
                        } else if (result.dismiss === 'cancel') {
                            swal.fire(
                                'Hapus Data Dibatalkan',
                                '',
                                'error'
                            )
                        }
                    });
                }
            },
            error: (err) => {

            }
        });
    });

    $("#table_data").on("click", "#delete_subdepartment", function() {
        let subdepartment_id = $(this).data("id");
        console.log(subdepartment_id);
        $.ajax({
            url: "master/department/check_id_subdepartment",
            type: "GET",
            dataType: "JSON",
            data: {
                subdepartment_id: subdepartment_id
            },
            success: (data) => {
                console.log(data);
                if (data.status.code != 200) {
                    toastr.error(data.status.message);
                } else {
                    swal.fire({
                        title: 'Hapus Data?',
                        text: "Data yang dihapus sudah tidak dapat dikembalikan lagi",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                        reverseButtons: true
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                url: "master/department/delete_subdepartment_process",
                                method: "POST",
                                dataType: "JSON",
                                data: {
                                    subdepartment_id: subdepartment_id
                                },
                                success: (data) => {
                                    if (data.status.code == 200) {
                                        toastr.success(data.status.message);
                                        table.ajax.reload();
                                    } else {
                                        toastr.error(data.status.message);
                                    }
                                },
                                error: (err) => {
                                    toastr.error(err.responseText);
                                }
                            })
                        } else if (result.dismiss === 'cancel') {
                            swal.fire(
                                'Hapus Data Dibatalkan',
                                '',
                                'error'
                            )
                        }
                    });
                }
            },
            error: (err) => {
                toastr.error(err.responseText);
            }
        });
    });
});