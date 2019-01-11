/**/
    	window.onload = function(){
    		document.getElementById("select").value = "101";
            document.getElementById("yomikomi").style.display = "none";
    	}
        function clickBtn(){
            const str = document.getElementById("select").value;

            for (var i = 0; i < 9; i++) {
            
            
                if ((i+101) != str) {
                    document.getElementById(i+101).style = "display: none;";
                } else {
                    document.getElementById(str).style = "display: block;";
                }

            }

        }