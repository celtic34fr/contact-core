// ************************ Drag and drop ***************** //

const removeButton = document.createElement('span');
removeButton.setAttribute("class", "removeBtn fa fa-times text-danger");

const viewButton = document.createElement('span');
viewButton.setAttribute("class", "viewBtn fa fa-eyes text-info");

// removeButton.classList.add('fa fa-times hidden text-danger');

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;

    handleFiles(files, this);
}

// let uploadProgress = [];
// let progressBar = document.getElementById('progress-bar');

function initializeProgress(numFiles, objId) {
    const object = document.querySelector('#' + objId).parentNode;
    const progressBar = object.querySelector('.progress');
    if (progressBar) {
        progressBar.value = 0;
        const uploadProgress = [];

        for (let i = numFiles; i > 0; i--) {
            uploadProgress.push(0);
        }

        let uploadProgressJSON = JSON.stringify(uploadProgress);
        object.setAttribute('data-upload-progress', uploadProgressJSON);
    }
}

function updateProgress(fileNumber, percent) {
    uploadProgress[fileNumber] = percent;
    let total = uploadProgress.reduce((tot, curr) => tot + curr, 0) / uploadProgress.length;
    console.debug('update', fileNumber, percent, total);
    progressBar.value = total;
}

function handleFiles(files, obj) {
    files = [...files];
    if (typeof obj !== 'string') {
        obj = obj.id;
        let tiret = obj.indexOf('-');
        if (tiret !== -1) {
            obj = obj.substr(0, tiret);
        }
    }

    const acceptFiles = document.querySelector('#'+obj).getAttribute('accept');

    let drop = document.querySelector("#" + obj);
    let maxFile = drop.getAttribute('data-max-file') != 'xx' ?
                                                    parseFloat(drop.getAttribute('data-max-file')) : 'xx';
    let maxSize = parseFloat(document.querySelector('#'+obj).getAttribute('data-max-size'));
    let size = maxSize * 1024 * 1024;
    let galleryFiles = document.querySelectorAll("#" + obj + "-gallery img");
    let countFileLoaded = galleryFiles.length;
    let countFiles = files.length;

    let restToLoad = maxFile !== 'xx' ? maxFile - countFileLoaded : -1;

    if (restToLoad === 0) {
        alert(maxFile + " fichier(s) téléchargé(s), maximum autrisé, vous ne pouvez plus en télécharger d'autres");
    } else if (restToLoad < countFiles && maxFile !== 'xx' ) {
        alert('Plus que ' + restToLoad + ' fichier(s) a télécharger, veuillez refaire votre choix');
    } else {
        initializeProgress(files.length, obj);
        let i = 0;
        for (const file of files) {
            let ext = file.name.split('.')[1];
            if (acceptFiles.includes(ext)) {
                if (file.size <= size) {
                    uploadFile(file, obj).then(rc_call => {
                        console.log(rc_call);
                        let ref_id = use_result(rc_call, obj);
                        console.log('success');
                        countFileLoaded = previewFile(file, obj, countFileLoaded, ref_id);
                    }).catch(error => {
                        console.log(error);
                        return false;
                    });
                    i++;
                } else {
                    alert("fichier "+file.name+" trop lourd (max "+ maxSize +"Mo), veuillez recommencer");
                    return false;
                }
            } else {
                alert("fichier "+file.name+" ayant l'extension "+ext+" non supportée, veuillez recommencer");
                return false;
            }
        }
    }
}

function previewFile(file, objId, countFile, preview_ref) {
    let gallery = document.querySelector("#" + objId + "-gallery");
    let removeBtn = document.querySelector("#" + objId).getAttribute('data-remove-btn');
    let viewBtn = document.querySelector("#" + objId).getAttribute('data-view-btn');
    if (gallery) {
        countFile = countFile + 1;
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function () {
            let div = document.createElement('div');
            div.classList.add('img-preview');
            div.setAttribute('data-nbr', countFile);
            let img = document.createElement('img');
            let div_over = document.createElement('div');
            div_over.classList.add('over');

            img.src = reader.result;
            if (removeBtn) {
                let remove = document.createElement('span');
                remove.setAttribute('data-obj', objId);
                remove.setAttribute('data-nbr', countFile);
                remove.setAttribute('data-fid', preview_ref);
                remove.classList.add('fa', 'fa-times', 'btn', 'text-danger', 'removeBtn');
                div_over.appendChild(remove);
            }
            if (viewBtn) {
                let view = document.createElement('span');
                view.setAttribute('data-obj', objId);
                view.setAttribute('data-nbr', countFile);
                view.setAttribute('data-fid', preview_ref);
                view.classList.add('fa', 'fa-eye', 'btn', 'text-primary', 'viewBtn');
                div_over.appendChild(view);
            }
            if (removeBtn || viewBtn) {
                div.appendChild(div_over);
            }
            div.appendChild(img);
            gallery.appendChild(div);
        }
    }
    return countFile;
}

async function uploadFile(file, objet) {
    const url = document.querySelector('#'+objet).getAttribute('data-url');
    const acceptFiles = document.querySelector('#'+objet).getAttribute('accept');
    const formData = new FormData();

    formData.append('upload_preset', 'ujpu6gyk');
    formData.append('file', file);
    formData.append('object', objet);
    formData.append('acceptFiles', acceptFiles);

    const rc_call = await fetch(url, {
        method: 'POST',
        body: formData
    });
    return await rc_call.json();
}

// function uploadFile_old(file, objet, i, countFileLoaded) {
    // const url = '/backadmin/parans/upload-img-promo';
    // const url = document.querySelector('#'+objet).getAttribute('data-url');
    // const acceptFiles = document.querySelector('#'+objet).getAttribute('accept');
    // const xhr = new XMLHttpRequest();
    // const formData = new FormData();
    // xhr.open('POST', url, false);
    // xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    //
    // xhr.onreadystatechange = function (node) {
    //     if (xhr.readyState === 4) {
    //         if (xhr.status === 200) {
    //             let data = JSON.parse(xhr.responseText);
    //             if (typeof (data.error) !== 'undefined') {
    //                 console.log(data.error);
    //             } else {
    //                 let ref_id = use_result(data);
    //                 console.log('success');
    //                 previewFile(file, objet, countFileLoaded, ref_id);
    //             }
    //         } else {
    //             console.log("Error", xhr.statusText);
    //         }
    //     }
    // }

    // Update progress (can be used to show progress indicator)
    // xhr.upload.addEventListener("progress", function (e) {
    //     updateProgress(i, (e.loaded * 100.0 / e.total) || 100);
    // })

    // xhr.addEventListener('readystatechange', function (e) {
    //     if (xhr.readyState === 4 && xhr.status === 200) {
    //         updateProgress(i, 100) // <- Add this;
    //     } else if (xhr.readyState === 4 && xhr.status !== 200) {
    //         // Error. Inform the user
    //     }
    // })

    // formData.append('upload_preset', 'ujpu6gyk');
    // formData.append('file', file);
    // formData.append('object', objet);
    // formData.append('acceptFiles', acceptFiles);
    // xhr.send(formData);
// }

function clickHandle(event) {
    if (event.target.matches('div.drop-area')) {

    }
    if (event.target.matches('span.viewBtn')) {
    }
    if (event.target.matches('span.removeBtn')) {
        // console.log(event.target.parentNode);
        let objt = event.target.getAttribute('data-obj');
        let fid = event.target.getAttribute('data-fid');
        let preview = event.target.parentNode.parentNode;
        let ref = get_hidden(objt);
        let obj = document.querySelector('#' + objt);
        if (ref !== undefined) {
            remove_preview(ref, obj, fid);
        }
        preview.remove();
    }
    return;
}

for (const dropArea of document.querySelectorAll('.drop-area')) {
    // Prevent default drag behaviors
    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    })

    // Highlight drop area when item is dragged over it
    ;['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, function () {
            this.classList.add('highlight');
        }, false);
    })

    ;['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, function () {
            this.classList.remove('highlight');
        }, false);
    })

    // Handle dropped files
    dropArea.addEventListener('drop', handleDrop, false);
}

document.addEventListener('click', clickHandle, false);