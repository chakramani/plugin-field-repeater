jQuery(document).ready(function () {
    jQuery('#addRow').on('click', function (e) {
        let lastRow = jQuery('.row:last').html();
        console.log(lastRow);
        var matchedString = lastRow.match(/<div class="count">([0-9]*)<\/div>/);
        var matchedInputField = lastRow.match(/value="([0-9]*)">/);
        console.log(matchedInputField);

        if (matchedString.length) {
            let nextNumber = matchedString[1] * 1;
            nextNumber++
            var newRow = lastRow.replace(matchedString[0], '<div class="count">' + nextNumber + '</div>');
        }

        jQuery('#inputs').append('<div class="container row">' + newRow + '</div>');
        return false;
    });
    jQuery(document).on('click' ,'.remove' , function(){
        jQuery(this).parent().remove();
        return false;
    });
});
