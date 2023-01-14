let table = $(".quiz_assign_table").DataTable({
    bLengthChange: false,
    bDestroy: true,
    language: {
        emptyTable: "No data available in the table",
        search: "<i class='ti-search'></i>",
        searchPlaceholder: "Quick Search",
        paginate: {
            next: "<i class='ti-arrow-right'></i>",
            previous: "<i class='ti-arrow-left'></i>",
        },
    },
    dom: "Bfrtip",
    buttons: [
        {
            extend: "copyHtml5",
            text: '<i class="far fa-copy"></i>',
            title: $("#logo_title").val(),
            titleAttr: "Copy",
            exportOptions: {
                columns: ":visible",
                columns: ":not(:last-child)",
            },
        },
        {
            extend: "excelHtml5",
            text: '<i class="far fa-file-excel"></i>',
            titleAttr: "Excel",
            title: $("#logo_title").val(),
            margin: [10, 10, 10, 0],
            exportOptions: {
                columns: ":visible",
                columns: ":not(:last-child)",
            },
        },
        {
            extend: "csvHtml5",
            text: '<i class="far fa-file-alt"></i>',
            titleAttr: "CSV",
            exportOptions: {
                columns: ":visible",
                columns: ":not(:last-child)",
            },
        },
        {
            extend: "pdfHtml5",
            text: '<i class="far fa-file-pdf"></i>',
            title: $("#logo_title").val(),
            titleAttr: "PDF",
            exportOptions: {
                columns: ":visible",
                columns: ":not(:last-child)",
            },
            orientation: "landscape",
            pageSize: "A4",
            margin: [0, 0, 0, 12],
            alignment: "center",
            header: true,
            customize: function (doc) {
                doc.content[1].table.widths = Array(
                    doc.content[1].table.body[0].length + 1
                )
                    .join("*")
                    .split("");
            },
        },
        {
            extend: "print",
            text: '<i class="fa fa-print"></i>',
            titleAttr: "Print",
            title: $("#logo_title").val(),
            exportOptions: {
                columns: ":not(:last-child)",
            },
        },
        {
            extend: "colvis",
            text: '<i class="fa fa-columns"></i>',
            postfixButtons: ["colvisRestore"],
        },
    ],
    columnDefs: [
        {
            visible: false,
        },
    ],
    responsive: true,
});

$("body").on("click", ".selectAllQuiz", function () {
    let totalQuestions = $("#totalQuestions");
    let totalMarks = $("#totalMarks");

    let online_exam_id = $("#online_exam_id").val();
    let ques_assign = $(".ques_assign").val();
    let token = $(".csrf_token").val();
    let selectedQus = [];

    if ($(".selectAllQuiz").is(":checked") == true) {
        table
            .rows()
            .nodes()
            .to$()
            .find('input[type="checkbox"].question')
            .each(function () {
                $(this).prop("checked", true);
            });
    } else {
        table
            .rows()
            .nodes()
            .to$()
            .find('input[type="checkbox"].question')
            .each(function () {
                $(this).prop("checked", false);
            });
    }

    table
        .rows()
        .nodes()
        .to$()
        .find('input[type="checkbox"].question')
        .each(function () {
            if ($(this).is(":checked") == true) {
                selectedQus.push($(this).val());
            }
        });
    console.log("question : "+selectedQus);
    $.ajax({
        type: "POST",
        url: ques_assign,
        data: {
            _token: token,
            online_exam_id: online_exam_id,
            questions: selectedQus,
        },
        success: function (data) {
            console.log("test");
            totalQuestions.html(data.totalQus);
            totalMarks.html(data.totalMarks);
            toastr.success("Successfully Assign", "Success");
        },
        error: function (data) {
            console.log("error");
            toastr.error("Something went wrong!", "Error Alert");
            location.reload();
        },
    });
});
//
$("body").on("click", ".question", function () {
    assignQuiz();
});

function assignQuiz() {
    let totalQuestions = $("#totalQuestions");
    let totalMarks = $("#totalMarks");

    let online_exam_id = $("#online_exam_id").val();
    let ques_assign = $(".ques_assign").val();
    let token = $(".csrf_token").val();
    let selectedQus = [];

    //todo check only question
    table
        .rows()
        .nodes()
        .to$()
        .find('input[type="checkbox"].question')
        .each(function () {
            if ($(this).is(":checked") == true) {
                selectedQus.push($(this).val());
            }
        });

    console.log(selectedQus);

    if (!$(this).is(":checked")) {
        $("#questionSelectAll").prop("checked", false);
    }
    $.ajax({
        type: "POST",
        url: ques_assign,
        data: {
            _token: token,
            online_exam_id: online_exam_id,
            questions: selectedQus,
        },
        success: function (data) {
            totalQuestions.html(data.totalQus);
            totalMarks.html(data.totalMarks);
            // console.log(data.success);
            if (data.success==="Operation successful") {
                toastr.success("Successfully Assign", "Success");
            } else {
                toastr.error(data.success, "Warning");
            }
        },
        error: function (data) {
            toastr.error("Something went wrong!", "Error Alert");
            location.reload();
        },
    });
}

function setQuestionTime() {
    var checkStatus = document.getElementById("set_question_time").checked;
    var perQTime = document.getElementById("per_question_time");
    var totalQTime = document.getElementById("total_question_time");
    if (checkStatus) {
        perQTime.style.display = "block";
        totalQTime.style.display = "none";
    } else {
        perQTime.style.display = "none";
        totalQTime.style.display = "block";
    }
}

function changeQuestionReview() {
    var checkStatus = document.getElementById("questionReview").checked;
    var showResult = document.getElementById("showResultDiv");
    if (checkStatus) {
        showResult.style.display = "none";
    } else {
        showResult.style.display = "block";
    }
}
