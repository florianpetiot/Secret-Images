function traitementImage(event) {

    if (event.target.files[0] !== undefined) {

        if (event.target.files[0].type != "image/png") {
            alert("Le format de l'image doit être PNG");
            return;
        }

        document.getElementById("texte-depot-image").setAttribute("hidden", "");
        document.getElementById("plus-depot-image").setAttribute("display", "none");

         // afficher l'image dans la case
        let temp = document.getElementById('depot-image')

        // supprimer l'ancienne image
        if (document.getElementsByTagName('img')[0] !== undefined) {
            document.getElementById('depot-image').removeChild(document.getElementsByTagName('img')[0]);
        }

        temp.innerHTML += "<img src='"+ URL.createObjectURL(event.target.files[0])+ "' alt='Image'>";
    }
    else {
        // reinitialiser l'inteface
        document.getElementById("texte-depot-image").removeAttribute("hidden");
        document.getElementById("plus-depot-image").removeAttribute("display");
        document.getElementById('depot-image').removeChild(document.getElementsByTagName('img')[0]);

        document.getElementById('bouton1').setAttribute("hidden", "");
        document.getElementById('bouton2').setAttribute("hidden", "");
        let input1 = document.getElementById('input1')
        let input2 = document.getElementById('input2')

        input1.setAttribute("disabled", "");
        input2.setAttribute("disabled", "");
        input1.value = "";
        input2.value = "";
        input1.placeholder = "";
        input2.placeholder = "";
        return;
    }

   

    let formData = new FormData();
    formData.append('file', event.target.files[0]);

    let xmlHttp = new XMLHttpRequest();
    xmlHttp.open('POST', 'recuperer_image.php');
    xmlHttp.send(formData);

    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

            if (this.responseText == "err_signature") {

                // ajouter placeholder à #input1
                let input1 = document.getElementById('input1');
                input1.placeholder = "Ajouter un mot de passe";
                input1.value = "";
                input1.removeAttribute("disabled");

                let input2 = document.getElementById('input2');
                input2.placeholder = "Cacher un texte";
                input2.value = "";
                input2.removeAttribute("disabled");

                document.getElementById('bouton1').setAttribute("hidden", "");
                document.getElementById('bouton2').removeAttribute("hidden");
            }

            else if (this.responseText == "mdp") {
                let input1 = document.getElementById('input1');
                input1.placeholder = "entrer le mot de passe";
                input1.value = "";
                input1.removeAttribute("disabled");

                document.getElementById('bouton3').removeAttribute("hidden");
            }

            else {
                document.getElementById('input2').value = this.responseText;
                document.getElementById('bouton1').removeAttribute("hidden");
                document.getElementById('bouton2').setAttribute("hidden", "");
            }
        }
    }

}



function encoder() {


    let formData = new FormData();
    formData.append('input1', document.getElementById('input1').value);
    formData.append('input2', document.getElementById('input2').value);
    formData.append('file', document.getElementById('image').files[0]);

    let xmlHttp = new XMLHttpRequest();
    xmlHttp.open('POST', 'encoder_image.php');
    xmlHttp.send(formData);

    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

            if (this.responseText == "err_small") {
                alert("L'image est trop petite pour contenir le texte");
                return;
            }

            // the response is a file location
            // make a link to download the file and click it
            let a = document.createElement('a');
            a.href = this.responseText;
            a.download = "New_Secret_Image.png"
            a.click();

            // remove the link
            a.remove();

            let xmlHttp2 = new XMLHttpRequest();
            xmlHttp2.open('GET', 'unlink.php?fichier='+this.responseText);
            xmlHttp2.send();
        }
    }
}



function brouillerImage() {
    
    let formData = new FormData();
    formData.append('file', document.getElementById('image').files[0]);

    let xmlHttp = new XMLHttpRequest();
    xmlHttp.open('POST', 'brouiller_image.php');
    xmlHttp.send(formData);

    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // the response is a file location
            // make a link to download the file and click it
            let a = document.createElement('a');
            a.href = this.responseText;
            a.download = "Normal Image.png"
            a.click();

            // remove the link
            a.remove();

            let xmlHttp2 = new XMLHttpRequest();
            xmlHttp2.open('GET', 'unlink.php?fichier='+this.responseText);
            xmlHttp2.send();
        }
    }
}



function decoderMdp() {
    let formData = new FormData();
    formData.append('input1', document.getElementById('input1').value);
    formData.append('file', document.getElementById('image').files[0]);

    let xmlHttp = new XMLHttpRequest();
    xmlHttp.open('POST', 'decoder_mdp.php');
    xmlHttp.send(formData);

    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == "err_mdp") {
                alert("Mot de passe incorrect");
                return;
            }
            document.getElementById('input1').placeholder = "";
            document.getElementById('input1').value = "";
            document.getElementById('input1').setAttribute("disabled", "");
            document.getElementById('input2').value = this.responseText;
            document.getElementById('bouton1').removeAttribute("hidden");
            document.getElementById('bouton3').setAttribute("hidden", "");
        }
    }
}