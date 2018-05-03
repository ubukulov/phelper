// function lists_of_institute() {
//     var id_institute = $('#id_institute').val();
//     $.get("/institute/"+id_institute, id_institute, function(data){
//         data = JSON.parse(data);
//         var html = "<option value='0'>Укажите кафедру</option>";
//         for (var i = 0; i < data.length; i++) {
//             html = html + "<option value='" + data[i].cafedraID + "'>"+data[i].cafedraNameRU+"</option>";
//         };
//         $("#id_chair").html(html);
//     });
// }
// function lists_of_chair() {
//     var id_chair = $('#id_chair').val();
//     $.get("/chair/"+id_chair, id_chair, function (data) {
//         data = JSON.parse(data);
//         var html = "<option value='0'>Укажите специальность</option>";
//         //var html = "";
//         for (var i = 0; i < data.length; i++) {
//             html = html + "<option value='" + data[i].professionID + "'>"+data[i].professionName+"</option>";
//         };
//         $("#id_profession").html(html);
//     });
// }
// function lists_of_profession() {
//     var id_study_form = $('#id_study_form');
//     var id_course = $('#id_course');
// }
function lists_of_teacher_and_stream() {
    var id_subject = $('#id_subject').val();
    if(id_subject == 0){
        $("#id_stream").html('');
    }else{
        $.get("/teacher/"+id_subject, function (data) {
            data = JSON.parse(data);
            var html = "";
            for (var i = 0; i < data.length; i++) {
                html = html + "<option class='item_value' value='" + data[i].stream + "'>"+data[i].tutor+" ("+data[i].stream+")"+"</option>";
            };
            $("#id_stream").html(html);
            if(html.length > 0){
                $("#submit").prop({
                    disabled: false
                });
            }
        });
    }
}

function list_of_study_forms() {
    var study_form = $("#study_form");
    var id_profession = $('#id_profession').val();
    if(id_profession == 0){
        study_form.html('');
    }else{
        $.get("/study/"+id_profession, function (data) {
            study_form.html('');
            data = JSON.parse(data);

            var nOldFO=0;
            for (var i = 0; i < data.length; i++) {
               if (nOldFO != data[i].s){
                   if (nOldFO!=0) {
                       $("#study_form").append(full) ;
                   }
                   var html = "";
                   var full=document.createElement('div');
                       full.setAttribute('class','form-check');
                       full.innerHTML=data[i].n+" | ";

                   var input_check=document.createElement('input');
                       input_check.setAttribute('type','checkbox');
                       input_check.setAttribute('name','full'+data[i].s+'[]');
                       input_check.setAttribute('value',data[i].c);
                   var span=document.createElement('span');

                   span.innerHTML = data[i].c;
                       full.appendChild(input_check);
                       full.appendChild(span);


               } else {
                   var input_check=document.createElement('input');
                       input_check.setAttribute('type','checkbox');
                   input_check.setAttribute('name','full'+data[i].s+'[]');
                   input_check.setAttribute('value',data[i].c);
                   var span=document.createElement('span');

                   span.innerHTML = data[i].c;
                       full.appendChild(input_check);
                   full.appendChild(span);
               }
                nOldFO=data[i].s;
            }

            if (nOldFO!=0) {
                $("#study_form").append(full) ;
            }
        });
    }
}
function contingent() {
    var id_tutor = $("#id_tutor").val();
    var contingent = $("#contingent");
    var count = $("#count");
    $.get("/contingent/"+id_tutor, function(data){
        data = JSON.parse(data);
        contingent.html('');
        count.html('');
        var html = "";
        var sum = 0;
        for(var i = 0; i < data.length; i++){
            html = html + '<tr><td>'+data[i].pname+'</td><td>'+data[i].sname+'</td><td>'+data[i].course+'</td><td>'+data[i].count+'</td><td><a href="/contingent/delete/' + data[i].id + '"">Удалить</a></td></tr>';
            sum = sum + data[i].count;
        }
        contingent.html(html);
        count.html(sum);
    });
}
function study_forms(){
    $('#id_study_form').dropdown('clear').find('.item_value').remove();
    $('#id_course').dropdown('clear').find('.item_value').remove();
    $('#id_subject').dropdown('clear').find('.item_value').remove();
    $('#id_stream').dropdown('clear').find('.item_value').remove();
    var id_profession = $('#id_profession').val();
    $.get("/contingent/profession/"+id_profession, function(data){
        data = JSON.parse(data);
        var html = "<option class='default_value' value=''></option>";
        for(var i = 0; i < data.length; i++){
            html = html + '<option class="item_value" value="'+data[i].id+'">'+data[i].name+'</option>';
        }
        $('#id_study_form').html(html);
    });
}
function specialization(){
    var id_profession = $('#id_profession').val();
    $.get("/specialization/profession/"+id_profession, function(data){
        data = JSON.parse(data);
        var html = "<option class='default_value' value=''></option>";
        for(var i = 0; i < data.length; i++){
            html = html + '<option class="item_value" value="'+data[i].id+'">'+data[i].nameru+'</option>';
        }
        $('#id_specialization').html(html);
    });
}
function list_groups(){
    var id_specialization = $('#id_specialization :selected').val();
    $.get("/group/specialization/"+id_specialization, function(data){
        data = JSON.parse(data);
        var html = "<option class='default_value' value=''></option>";
        for(var i = 0; i < data.length; i++){
            html = html + '<option class="item_value" value="'+data[i].id+'">'+data[i].nameru+'</option>';
        }
        $('#id_group').html(html);
    });
}
function list_course(){
    var id_study_form = $('#id_study_form').val();
    var id_profession = $('#id_profession').val();
    var id_course = $('#id_course');
    // if(id_study_form == 0){
    //     id_course.html('');
    // }
    $.get("/contingent/profession/"+id_profession+"/studyform/"+id_study_form, function(data){
        data = JSON.parse(data);
        //id_course.html('');
        var html = "";
        for(var i = 0; i < data.length; i++){
            if(data[i].course == 4){
                html = html + '<option class="item_value" value="'+data[i].course+'">4 (лотус)</option>';
            }else{
                html = html + '<option class="item_value" value="'+data[i].course+'">'+data[i].course+'</option>';
            }
        }
        id_course.html(html);
    });
}
function statement(){
    var id_profession = $('#id_profession').val();
    var id_study_form = $('#id_study_form').val();
    var id_course = $('#id_course').val();
    var id_term = $('#id_term').val();
    var id_study_lang = $('#id_study_lang').val();
    var id_subject = $('#id_subject');
    $.get("/vedomost/"+id_profession+"/"+id_study_form+"/"+id_course+"/"+id_term+"/"+id_study_lang, function(data){
        id_subject.html('');
        data = JSON.parse(data);
        var html = "<option class='default_value' value=''></option>";
        for(var i = 0; i < data.length; i++){
            html = html + "<option class='item_value' value='" + data[i].id + "'>"+data[i].name+" ( "+data[i].code+" )"+" - (<mark>"+data[i].type+"</mark>)</option>";
        }
        id_subject.html(html);
    });
}
function individual(){
    var id_profession = $('#id_profession').val();
    var id_study_form = $('#id_study_form').val();
    var id_course = $('#id_course').val();
    var id_term = $('#id_term').val();
    var id_study_lang = $('#id_study_lang').val();
    var id_subject = $('#id_subject');
    var id_student = $('#id_student');
    var id_status = $('#id_status').val();
    var from_tran = 0;
    if($('#data_from_tran').prop('checked')){
        from_tran = 1;
    }else{
        from_tran = 0;
    }

    $.get("/individual/"+id_profession+"/"+id_study_form+"/"+id_course+"/"+id_study_lang+"/"+id_status+"/"+from_tran, function(data){
        id_student.html('');
        data = JSON.parse(data);
        var html = "";
        for(var i = 0; i < data.length; i++){
            var j;
            j = i + 1;
            html = html + "<option class='item_value' value='" + data[i].id + "'>"+j+")  "+data[i].fio+"  ("+data[i].id+")</option>";
        }
        id_student.html(html);
        $("#submit").prop({
            disabled: false
        });
    });
}
function check_form_data() {
    var submit = $('#submit');
    var id_profession = $('#id_profession').val();
    var id_course = $('#id_course').val();
    var id_subject = $('#id_subject').val();
    var stream = $('#id_stream').val();
}
function list_students(){
    var id_report  = $('#id_report').val();
    var discipline = $('#discipline');
    var str        = $('#str');
    var student  = $('#student');
    var individual  = $('#individual');
    var statement  = $('#statement');
    var data  = $('#data');
    var data1  = $('#data1');
    var data2  = $('#data2');
    var time  = $('#time');
    var branch  = $('#branch');
    var type  = $('#type');
    var term  = $('#term');
    var type_summary  = $('#type_summary');
    if(id_report == 1){
        discipline.show();
        str.show();
        statement.show();
        student.hide();
        individual.hide();
        data.hide();
        data1.show();
        data2.show();
        time.val('');
		branch.hide();
        type.show();
    }
    if(id_report == 2){
        discipline.show();
        str.show();
        statement.show();
        student.hide();
        individual.hide();
        time.val('');
        data.show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
    }
    if(id_report == 3){
        discipline.show();
        str.show();
        statement.show();
        student.hide();
        individual.hide();
        time.val('');
        data.show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
    }
    if(id_report == 4){
        discipline.show();
        str.show();
        statement.show();
        student.hide();
        individual.hide();
        time.val('');
        data.show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
    }
    if(id_report == 5){
        discipline.show();
        str.show();
        statement.show();
        student.hide();
        individual.hide();
        time.val('');
        data.hide();
        branch.show();
        data1.hide();
        data2.hide();
        type.hide();
    }
    if(id_report == 6){
        discipline.hide();
        str.hide();
        statement.hide();
        student.show();
        individual.show();
        time.val('');
        data.show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
    }
    if(id_report == 7){
        discipline.hide();
        str.hide();
        statement.hide();
        student.show();
        individual.show();
        time.val('');
        data.show();
        data1.hide();
        data2.hide();
		branch.hide();
		type.show();
    }
    if(id_report == 8){
        discipline.hide();
        str.hide();
        statement.hide();
        student.hide();
        individual.hide();
        time.val('');
        data.hide();
        $('#submit').hide();
        $('#summary').show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
        type_summary.show();
    }
    if(id_report == 9){
        discipline.hide();
        str.hide();
        statement.hide();
        student.hide();
        individual.hide();
        time.val('');
        data.hide();
        $('#submit').hide();
        $('#summary').show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
    }
    if(id_report == 10){
        discipline.show();
        str.show();
        statement.show();
        student.hide();
        individual.hide();
        time.val('');
        data.show();
        $('#submit').hide();
        $('#summary').show();
        data1.hide();
        data2.hide();
		branch.hide();
        type.hide();
    }
    if(id_report == 11){
        discipline.hide();
        str.hide();
        statement.hide();
        student.show();
        individual.show();
        time.val('');
        data.show();
        data1.hide();
        data2.hide();
        branch.hide();
        type.hide();
    }
    if(id_report == 12){
        discipline.hide();
        str.hide();
        statement.hide();
        student.hide();
        individual.hide();
        time.val('');
        data.hide();
        $('#submit').hide();
        $('#summary').show();
        data1.hide();
        data2.hide();
        branch.hide();
        type.hide();
        term.hide();
    }
}
// список студентов
function students(){
    var id_profession = $('#id_profession').val();
    var id_specialization = $('#id_specialization').val();
    var id_study_form = $('#id_study_form').val();
    var id_course = $('#id_course').val();
    var id_study_lang = $('#id_study_lang').val();
    var id_student = $('#id_student');

    if(id_profession == 0){
        alert("Выберите специальность");
    }
    if(id_study_form == 0){
        alert("Выберите форму обучения");
    }
    if(id_course == 0){
        alert("Выберите курс");
    }
    $.get("/student/list/"+id_profession+"/"+id_study_form+"/"+id_course+"/"+id_study_lang+'/'+id_specialization, function(data){
        id_student.html('');
        data = JSON.parse(data);
        var html = "";
        for(var i = 0; i < data.length; i++){
            var j;
            j = i + 1;
            html = html + "<option class='item_value' value='" + data[i].id + "'>"+j+")  "+data[i].fio+"  ("+data[i].id+")</option>";
        }
        id_student.html(html);
        $("#submit").prop({
            disabled: false
        });
    });
}

function isEmpty(obj) {

    if (obj == null) return true;

    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    if (typeof obj !== "object") return true;

    return true;
}

$( function() {
    $( "#time" ).datepicker();
    $( "#time1" ).datepicker();
    $( "#time2" ).datepicker();
    $.datepicker.regional['ru'] = {
        closeText: 'Закрыть',
        prevText: '&#x3C;Пред',
        nextText: 'След&#x3E;',
        currentText: 'Сегодня',
        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
            'Июл','Авг','Сен','Окт','Ноя','Дек'],
        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        weekHeader: 'Нед',
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['ru']);
} );

$(document).ready(function(){
    $('#js_loader').removeClass('js');
    $(".dropdown").dropdown();
    $('.form').form({
        fields: {
            id_profession: {
                identifier: 'id_profession',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please select a dropdown value'
                    }
                ]
            }
        }
    });

    $('.ui.checkbox').checkbox();
    // Приемная комиссия
    $('#KT').on('change', function(){
        var KT_FORM = $('#KT_FORM');
        if($(this).prop('checked')){
            KT_FORM.show();
        }else{
            KT_FORM.hide();
        }
    });
    $('#KT1').on('change', function(){
        var KT_FORM = $('#KT_FORM1');
        if($(this).prop('checked')){
            KT_FORM.show();
        }else{
            KT_FORM.hide();
        }
    });
    $('#KT2').on('change', function(){
        var KT_FORM = $('#KT_FORM2');
        if($(this).prop('checked')){
            KT_FORM.show();
        }else{
            KT_FORM.hide();
        }
    });
    $('#ENT').on('change', function(){
        var KT_FORM = $('#ENT_FORM');
        if($(this).prop('checked')){
            KT_FORM.show();
        }else{
            KT_FORM.hide();
        }
    });
    // Приемная комиссия
    var id_student = $('#st');
    id_student.dropdown({
        onChange:function(val){
            $.get("/user/kt/"+val, function(data){
                var student_result = [];
                var student_kt = [];
                var student_kt1 = [];
                var student_kt2 = [];
                var student_ent = [];

                $.each(data,function (key,value) {
                    if(key=='student_results'){
                        student_result = value;
                    }
                    if(key=='student_kt'){
                        student_kt = value;
                    }
                    if(key=='student_kt1'){
                        student_kt1 = value;
                    }
                    if(key=='student_kt2'){
                        student_kt2 = value;
                    }
                    if(key=='student_ent'){
                        student_ent = value;
                    }
                });
                // student_results
                if($.isEmptyObject(student_result) != true){
                    if(student_result.winner_olimp == 1){
                        $('#winner_olimp').prop('checked', true);
                        $('#winner_olimp_txt').val(student_result.winner_olimp_txt);
                    }
                    if(student_result.res_program == 1){
                        $('#res_program').prop('checked', true);
                        $('#res_program_txt').val(student_result.res_program_txt);
                    }
                    $('#type_sport').val(student_result.type_sport);
                    $('#sport_achievement').dropdown('set selected', student_result.sport_achievement);
                }else{
                    $('#winner_olimp').prop('checked', false); $('#winner_olimp_txt').val(''); $('#res_program').prop('checked', false); $('#res_program_txt').val(''); $('#type_sport').val('');
                }
                // kt
                if($.isEmptyObject(student_kt) != true){
                    $('#KT').prop('checked', true); $('#KT_FORM').show(); $('#seria_certificate').val(student_kt.seria_certificate); $('#number_certificate').val(student_kt.number_certificate); $('#number_ikt').val(student_kt.number_ikt);
                    $('#count_ball').val(student_kt.count_ball); $('#ball_math').val(student_kt.ball_math); $('#ball_history').val(student_kt.ball_history); $('#ball_read').val(student_kt.ball_read); $('#kt_profile_subject1').dropdown('set selected', student_kt.kt_profile_subject1);
                    $('#one_ball').val(student_kt.one_ball); $('#kt_profile_subject2').dropdown('set selected', student_kt.kt_profile_subject2); $('#two_ball').val(student_kt.two_ball); $('#kt_creative_exam1').dropdown('set selected', student_kt.kt_creative_exam1); $('#oneExamBall').val(student_kt.oneExamBall);
                    $('#kt_creative_exam2').dropdown('set selected', student_kt.kt_creative_exam2); $('#twoExamBall').val(student_kt.twoExamBall); $('#special_subject').val(student_kt.special_subject); $('#special_ball').val(student_kt.special_ball);
                }else{
                    $('#KT').prop('checked', false); $('#KT_FORM').hide(); $('#KT_FORM input').val('');
                }
                // kt1
                if($.isEmptyObject(student_kt1) != true){
                    $('#KT1').prop('checked', true); $('#KT_FORM1').show(); $('#seria_certificate1').val(student_kt1.seria_certificate); $('#number_certificate1').val(student_kt1.number_certificate); $('#number_ikt1').val(student_kt1.number_ikt);
                    $('#count_ball1').val(student_kt1.count_ball); $('#ball_math1').val(student_kt1.ball_math); $('#ball_history1').val(student_kt1.ball_history); $('#ball_read1').val(student_kt1.ball_read); $('#kt1_profile_subject1').dropdown('set selected', student_kt1.kt_profile_subject1);
                    $('#one_ball1').val(student_kt1.one_ball); $('#kt1_profile_subject2').dropdown('set selected', student_kt1.kt_profile_subject2); $('#two_ball1').val(student_kt1.two_ball); $('#kt1_creative_exam1').dropdown('set selected', student_kt1.kt_creative_exam1); $('#oneExamBall1').val(student_kt1.oneExamBall);
                    $('#kt1_creative_exam2').dropdown('set selected', student_kt1.kt_creative_exam2); $('#twoExamBall1').val(student_kt1.twoExamBall); $('#special_subject1').val(student_kt1.special_subject); $('#special_ball1').val(student_kt1.special_ball);
                }else{
                    $('#KT1').prop('checked', false); $('#KT_FORM1').hide(); $('#KT_FORM1 input').val('');
                }
                // kt2
                if($.isEmptyObject(student_kt2) != true){
                    $('#KT2').prop('checked', true); $('#KT_FORM2').show(); $('#seria_certificate2').val(student_kt2.seria_certificate); $('#number_certificate2').val(student_kt2.number_certificate); $('#number_ikt2').val(student_kt2.number_ikt);
                    $('#count_ball2').val(student_kt2.count_ball); $('#ball_math2').val(student_kt2.ball_math); $('#ball_history2').val(student_kt2.ball_history); $('#ball_read2').val(student_kt.ball_read); $('#kt2_profile_subject1').dropdown('set selected', student_kt2.kt_profile_subject1);
                    $('#one_ball2').val(student_kt2.one_ball); $('#kt2_profile_subject2').dropdown('set selected', student_kt2.kt_profile_subject2); $('#two_ball2').val(student_kt2.two_ball); $('#kt2_creative_exam1').dropdown('set selected', student_kt2.kt_creative_exam1); $('#oneExamBall2').val(student_kt2.oneExamBall);
                    $('#kt2_creative_exam2').dropdown('set selected', student_kt2.kt_creative_exam2); $('#twoExamBall2').val(student_kt2.twoExamBall); $('#special_subject2').val(student_kt2.special_subject); $('#special_ball2').val(student_kt2.special_ball);
                }else{
                    $('#KT2').prop('checked', false); $('#KT_FORM2').hide(); $('#KT_FORM2 input').val('');
                }
                // ent
                if($.isEmptyObject(student_ent) != true){
                    $('#ENT').prop('checked', true); $('#ENT_FORM').show(); $('#seria_certificate3').val(student_ent.seria_certificate); $('#number_certificate3').val(student_ent.number_certificate); $('#number_ikt3').val(student_ent.number_ikt);
                    $('#count_ball3').val(student_ent.count_ball); $('#ball_math3').val(student_ent.ball_math); $('#ball_history3').val(student_ent.ball_history); $('#ball_read3').val(student_ent.ball_read); $('#ent_profile_subject1').dropdown('set selected', student_ent.kt_profile_subject1);
                    $('#one_ball3').val(student_ent.one_ball); $('#ent_profile_subject2').dropdown('set selected', student_ent.kt_profile_subject2); $('#two_ball3').val(student_ent.two_ball); $('#ent_creative_exam1').dropdown('set selected', student_ent.kt_creative_exam1); $('#oneExamBall3').val(student_ent.oneExamBall);
                    $('#ent_creative_exam2').dropdown('set selected', student_ent.kt_creative_exam2); $('#twoExamBall3').val(student_ent.twoExamBall); $('#special_subject3').val(student_ent.special_subject); $('#special_ball3').val(student_ent.special_ball);
                }else{
                    $('#ENT').prop('checked', false); $('#ENT_FORM').hide(); $('#ENT_FORM input').val('');
                }
            });//.done(function(){
                //$('#preloader').fadeOut('slow',function(){$(this).remove();});
                //$('#js_loader').removeClass('js');
            //});//.always(function(){
                //$('#js_loader').addClass('js');
            //});
        }});
});
// итоговая ведомость
function summary_statement(){
    var id_profession = $('#id_profession').val();
    var id_study_form = $('#id_study_form').val();
    var id_specialization = $('#id_specialization').val();
    var id_course = $('#id_course').val();
    var id_term = $('#id_term').val();
    var id_study_lang = $('#id_study_lang').val();
    var id_subject = $('#id_subject');
    var id_stream = $('#id_stream');
    $.get("/user/summary/statement/"+id_profession+"/"+id_study_form+"/"+id_specialization+"/"+id_course+"/"+id_term+"/"+id_study_lang, function(data){
        id_subject.html('');
        data = JSON.parse(data);
        var html = "<option class='default_value' value=''></option>";
        for(var i = 0; i < data.length; i++){
            html = html + "<option class='item_value' value='" + data[i].id + "'>"+data[i].name+" ( "+data[i].code+" )"+" - (<mark>"+data[i].type+"</mark>)</option>";
        }
        id_subject.html(html);
    });
}