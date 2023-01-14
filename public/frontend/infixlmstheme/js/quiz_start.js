var back = false;

window.onbeforeunload = function (e) {
    if (back === true)
        return "Are you sure to exit?";
}


let runner;
let totalQus = $('.quiz_assign').val();
let presentTime = document.getElementById('timer').innerHTML;
let timeArray = presentTime.split(/[:]+/);
if (timeArray[0] == 0 && timeArray[1] == 0) {
    toastr.error('Time not define', 'Failed');
} else {
    // startTimer();
    $('#StartConfirmModal').modal('show');
}
$("#QuizStartBtn").click(function (e) {
    e.preventDefault();
    $('#StartConfirmModal').modal('hide');
    $('.quiz_test_body').removeClass('d-none');
    startQuiz();
    startTimer();
});

function startQuiz() {
    back = true;
    let url = $('input[name="quiz_start_url"]').val();
    let courseId = $('input[name="courseId"]').val();
    let quizId = $('input[name="quizId"]').val();
    let quizType = $('input[name="quizType"]').val();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: url,
        data: {
            'courseId': courseId,
            'quizId': quizId,
            'quizType': quizType,
            "_token": $('meta[name="csrf-token"]').attr('content')

        },
        success: function (data) {
            if (data.result) {
                $('input[name="quiz_test_id"]').val(data.data.id);
            }
        }
    });


}

//start timer
function startTimer() {
    var presentTime = document.getElementById('timer').innerHTML;
    var timeArray = presentTime.split(/[:]+/);

    var m = timeArray[0];
    var s = checkSecond((timeArray[1] - 1));
    // console.log(s);
    if (s == 59) {
        m = m - 1
    }
    if ((m + '').length == 1) {
        m = '0' + m;
    }
    if (m < 0) {
        back = false;
        $("#quizForm").submit();
        // submitQuiz();
        // m = '59';
    } else {
        document.getElementById('timer').innerHTML = m + ":" + s;
        runner = setTimeout(startTimer, 1000);
    }

}

//check secound
function checkSecond(sec) {
    if (sec < 10 && sec >= 0) {
        sec = "0" + sec;
    }
    // add zero in front of numbers < 10
    if (sec < 0) {
        sec = "59";
    }

    return sec;
}


$(".skip").click(function (e) {
    e.preventDefault();
    $('.question_number_lists .skip_qus').next('.nav-link').trigger('click');

});


$(".next").click(function (e) {
    e.preventDefault();


    let question_type = $(this).data('question_type');
    let question_id = $(this).data('question_id');
    let assign_id = $(this).data('assign_id');


    let ans = $(".tab-pane:visible").find('.quizAns:checked').val();

    if (ans == "undefined" || ans == "" || ans == null) {
        console.log(question_type)
        if (question_type == 'M') {
            toastr.error('Please select a option', 'Error Alert', {
                timeOut: 2000
            });
            return false;
        } else {
            let answer_input = $("#editor" + assign_id).val();
            if (answer_input == "undefined" || answer_input == "" || answer_input == null) {
                toastr.error('Please Write Answer', 'Error Alert', {
                    timeOut: 2000
                });
                return false;
            } else {
                questionSubmitSingle(question_type, assign_id);
                $('.question_number_lists .skip_qus').next('.nav-link').trigger('click');
                return true;
            }
        }

    } else {
        questionSubmitSingle(question_type, assign_id);

        $('.question_number_lists .skip_qus').next('.nav-link').trigger('click');
    }


});


// new feature


$(".quizSubmitClose").click(function (e) {
    $(".submitBtn").html('Submit');
});
$(".submitBtn").click(function (e) {
    e.preventDefault();

    let question_type = $(this).data('question_type');
    let assign_id = $(this).data('assign_id');
    questionSubmitSingle(question_type, assign_id);
    $(".submitBtn").html('Submitting..');
    setTimeout(function () {
        $('#submitConfirmModal').modal('show');
    }, 3000);


});

$("#QuizSubmitBtn").click(function (e) {
    e.preventDefault();
    back = false;
    $("#quizForm").submit();
});

$(".questionLink").click(function (e) {

    var element = $(this);
    var currentNumber = $('#currentNumber');
    var number = element.text();
    currentNumber.text(number);

    questionIsChecked();

    element.removeClass('skip_qus');
    element.removeClass('pouse_qus');
    element.addClass('skip_qus')

    $('.singleQuestion').each(function (i, obj) {
        var qus_id = $(this).data('qus-id');
        var link = $('#pills-' + qus_id);


        if (element.data('qus') === qus_id) {

            link.addClass('active');
            link.addClass('show');
        } else {
            link.removeClass('active');
            link.removeClass('show');
        }
    })
});


function questionIsChecked() {
    $('.singleQuestion').each(function (i, obj) {
        var qus_id = $(this).data('qus-id');
        var qus_type = $(this).data('qus-type');
        var link = $('.link_' + qus_id);

        link.removeClass('skip_qus');
        link.removeClass('pouse_qus');
        link.removeClass('active');


        if (qus_type == "M") {
            if ($(this).find(':checkbox').is(":checked")) {
                link.addClass('active');
            } else {
                link.addClass('pouse_qus');
            }
        } else {
            let answer_input = $("#editor" + qus_id).val();
            if (answer_input == "") {
                link.addClass('pouse_qus');
            } else {
                link.addClass('active');
            }

        }


    });
}


function questionSubmitSingle(question_type, assign_id) {
    let url = $('input[name="single_quiz_submit_url"]').val();
    let quiz_test_id = $('input[name="quiz_test_id"]').val();


    var data = [];

    if (question_type == "M") {
        $('.quizAns:checked').each(function (i) {
            if ($(this).is(":visible")) {
                data[i] = $(this).val();
            }
        });
    } else {
        data = $("#editor" + assign_id).val();
    }
    console.log(data);

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: url,
        data: {
            'quiz_test_id': quiz_test_id,
            'assign_id': assign_id,
            'type': question_type,
            'ans': data,
            "_token": $('meta[name="csrf-token"]').attr('content')

        },
        success: function (data) {
            console.log(data)

        }
    });

    return true;

}
