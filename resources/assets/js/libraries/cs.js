var cs = {

    isEmpty: function(str) {
        return (!str || 0 === str.length);
    },

    isUndefined: function(val){

       return (typeof val === 'undefined"') ? true : false;

    },

    toFixed: function(val, precision){

        if(precision == 0 || precision){
            precision = precision;
        }else{
            precision = 2;
        }

        var answer = 0.00;

        if(!this.isUndefined(val)){
            answer =  val.toFixed(precision);
        }

        return answer;

    },

    toNumber: function(val){

        var answer = 0;

        if(!this.isUndefined(val)){
            val = val.replace(',', '');
        }

        var number = Number(val);

        if(!isNaN(number)){
            answer = number;
        }

        return answer;

    },

    toFloat: function(val){

        var answer = 0;

        if(!this.isUndefined(val)){
            answer = val.replace(',', '');
            answer = parseFloat(answer);
        }

        return answer;

    },

    toLocalizeNumber: function(val, precision){

        if(precision == 0 || precision){
            precision = precision;
        }else{
            precision = 2;
        }

        var answer = 0.00;

        if(!this.isUndefined(val)){
            answer = this.toNumber(this.toFixed(val, precision)).toLocaleString(undefined, {minimumFractionDigits: precision});
        }

        return answer;

    },

    discount: function(val){
        return val <= 100 ? val : 100;
    },

    removeQuotes: function(str){
        if(typeof str === "string" && str.length > 0){
            str = str.replace(/(^"+)|("+$)/g, '');
        }

        return str;
    },

    sameDay: function(fdate, sdate){

        return fdate.getFullYear() === sdate.getFullYear()
            && fdate.getMonth() === sdate.getMonth()
            && fdate.getDate() === sdate.getDate();

    },

    getQueryString: function(field, url){

        var href = url ? url : window.location.href;
        var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
        var string = reg.exec(href);
        return string ? string[1] : null;

    }

}