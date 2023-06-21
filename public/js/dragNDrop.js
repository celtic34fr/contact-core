/*
let dropAreas = document.querySelectorAll('.drop-area');
let filesDone = 0;
let filesToDo = 0;
let progressBar = document.getElementById('progress-bar');
let uploadProgress = [];

function initializeProgress(numFiles, dropzone) {
    let progressBar = dropzone.querySelector(".progress-bar");
    progressBar.value = 0;
    uploadProgress = [];
  
    for(let i = numFiles; i > 0; i--) {
        uploadProgress.push(0);
    }
}
  
function updateProgress(fileNumber, percent, dropzone) {
    uploadProgress[fileNumber] = percent;
    let total = uploadProgress.reduce((tot, curr) => tot + curr, 0) / uploadProgress.length;
    let progressBar = dropzone.querySelector(".progress-bar");
    progressBar.value = total;
}

function preventDefaults (e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    e.target.classList.add('highlight');
}
  
function unhighlight(e) {
    e.target.classList.remove('highlight');
}

function handleDrop(e) {
    let dt = e.dataTransfer;
    let files = dt.files;
  
    handleFiles(files, e.target);
}

function handleFiles(files, dropzone) {
    files = [...files];
    initializeProgress(files.length, dropzone);
    Object.entries(files).forEach(entry => function(entry, dropzone) {
        const(i, file) = entry;
        uploadFile(file, i, dropzone);
    );
    files.forEach(file => previewFile(file, dropzone));
}  

function uploadFile(file, i, dropzone) { // <- Add `i` parameter
    var url = '/parameters/uploadLogo';
    var xhr = new XMLHttpRequest();
    var formData = new FormData();
    xhr.open('POST', url, true);
  
    // Add following event listener
    xhr.upload.addEventListener("progress", function(e) {
        updateProgress(i, (e.loaded * 100.0 / e.total) || 100, dropzone);
    })
  
    xhr.addEventListener('readystatechange', function(e) {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Done. Inform the user
        }
        else if (xhr.readyState == 4 && xhr.status != 200) {
            // Error. Inform the user
          }
    })
  
    formData.append('file', file);
    xhr.send(formData);
}

function previewFile(file, dropzone) {
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onloadend = function() {
        let img = document.createElement('img');
        img.src = reader.result;
        dropzone.querySelector('.gallery').appendChild(img);
    }
}
  
dropAreas.forEach(droparea => function() {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    dropArea.addEventListener('drop', handleDrop, false);
});
*/

// nouveaux modules

function file_explorer(drop_area) {
    let zone = document.querySelector("#"+drop_area);
    zone.querySelector('.selectFile').click();
}