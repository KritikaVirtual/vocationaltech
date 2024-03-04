function get_obj(object_name){
	var res = document.getElementById(object_name);
	return res;
}

function enc_elm(elm_id){
	return encodeURIComponent($("#" + elm_id).val());
}

function get_elm_val(elm_id){
	return $("#" + elm_id).val();
}

function err_ctc(str){
	if(str != "")
		throw new Error(str);
}

function err_str(e){
	return e.message;
}

function err_clr(elm_id){
	$("#" + elm_id).html("");
}

function err_dsp(e, elm_id){
	if(typeof(e) == "object")
		$("#" + elm_id).html(err_str(e));
	else
		$("#" + elm_id).html(e);
}

function check_all(field, flag){
	var tmp;
	var i=0;
	do{
		tmp = get_obj(field + "_" + i.toString());
		tmp.checked = flag;
		i++;
		tmp = get_obj(field + "_" + i.toString());
	}while(tmp);
}

function str_replace(str,sr,val){
	return str.replace(sr, val, "g");	
}

function to_int(str){
	res = parseInt(to_num(str));
	if(isNaN(res))
		res = 0;
	return res;
}

function to_num(str){
	res = parseFloat(str);
	if(isNaN(res))
		res = 0;
	return res;
}