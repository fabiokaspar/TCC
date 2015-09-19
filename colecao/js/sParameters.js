var parameters = [];
$(document).ready(function () {
    $(".param").click(selectParameter);
});
function selectParameter() {
    var value = $(this).data("value");
    if($(this).hasClass("selected")) {
        $(this).removeClass("selected");
        var index = parameters.indexOf(value);
        if (index > -1) {
            parameters.splice(index, 1);
        }
        $(this).find(".order").hide(100,function() {
            $(this).html("");
        });
    } else {
        $(this).addClass("selected");
        parameters.push(value);
    }
    console.log(JSON.stringify(parameters));
    putFeedbackNumbers();
}
function putFeedbackNumbers() {
    $(".param .order").html("");
    for(var i = 0; i < parameters.length; i++) {
        var value = parameters[i];
        var param = $(".param[data-value="+value+"] .order");
        $(param).html(i+1).show(100);
    }
}
function getParameters() {
    return parameters;
}