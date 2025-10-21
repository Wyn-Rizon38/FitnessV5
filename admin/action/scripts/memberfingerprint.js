let test = null;

let state = document.getElementById('content-capture');

let myVal = ""; // Drop down selected value of reader 
let disabled = true;
let startEnroll = false;

let currentFormat = Fingerprint.SampleFormat.PngImage;
const deviceTechn = {
    0: "Unknown",
    1: "Optical",
    2: "Capacitive",
    3: "Thermal",
    4: "Pressure"
};

const deviceModality = {
    0: "Unknown",
    1: "Swipe",
    2: "Area",
    3: "AreaMultifinger"
};

const deviceUidType = {
    0: "Persistent",
    1: "Volatile"
};

const FingerprintSdkTest = (function () {
    function FingerprintSdkTest() {
        this.operationToRestart = null;
        this.acquisitionStarted = false;
        this.sdk = new Fingerprint.WebApi;
        this.sdk.onDeviceConnected = function (e) {
            showMessage("Scan your finger");
        };
        this.sdk.onDeviceDisconnected = function (e) {
            showMessage("Device disconnected");
        };
        this.sdk.onCommunicationFailed = function (e) {
            showMessage("Communication Failed");
        };
        this.sdk.onSamplesAcquired = function (s) {
            sampleAcquired(s);
        };
        this.sdk.onQualityReported = function (e) {
            document.getElementById("qualityInputBox").value = Fingerprint.QualityCode[(e.quality)];
        };
    }

    FingerprintSdkTest.prototype.startCapture = function () {
        if (this.acquisitionStarted) return;
        showMessage("");
        this.operationToRestart = this.startCapture;
        this.sdk.startAcquisition(currentFormat, myVal).then(() => {
            this.acquisitionStarted = true;
            disableEnableStartStop();
        }, function (error) {
            showMessage(error.message);
        });
    };
    FingerprintSdkTest.prototype.stopCapture = function () {
        if (!this.acquisitionStarted) return;
        showMessage("");
        this.sdk.stopAcquisition().then(() => {
            this.acquisitionStarted = false;
            disableEnableStartStop();
        }, function (error) {
            showMessage(error.message);
        });
    };

    FingerprintSdkTest.prototype.getInfo = function () {
        return this.sdk.enumerateDevices();
    };

    FingerprintSdkTest.prototype.getDeviceInfoWithID = function (uid) {
        return this.sdk.getDeviceInfo(uid);
    };

    return FingerprintSdkTest;
})();

function showMessage(message) {
    // Fixed: declare x
    let x = state.querySelectorAll("#status");
    if (x.length !== 0) {
        x[0].innerHTML = message;
    }
}

window.onload = function () {
    localStorage.clear();
    test = new FingerprintSdkTest();
    readersDropDownPopulate(true);
    disableEnable();
    enableDisableScanQualityDiv("content-reader");
    disableEnableExport(true);
};

function onStart() {
    assignFormat();
    if (currentFormat === "") {
        alert("Please select a format.");
    } else {
        test.startCapture();
    }
}

function onStop() {
    test.stopCapture();
}

function onGetInfo() {
    let allReaders = test.getInfo();
    allReaders.then(function (sucessObj) {
        populateReaders(sucessObj);
    }, function (error) {
        showMessage(error.message);
    });
}

function onDeviceInfo(id, element) {
    let myDeviceVal = test.getDeviceInfoWithID(id);
    myDeviceVal.then(function (sucessObj) {
        let deviceId = sucessObj.DeviceID;
        let uidTyp = deviceUidType[sucessObj.eUidType];
        let modality = deviceModality[sucessObj.eDeviceModality];
        let deviceTech = deviceTechn[sucessObj.eDeviceTech];
        let retutnVal =
            "Id : " + deviceId +
            "<br> Uid Type : " + uidTyp +
            "<br> Device Tech : " + deviceTech +
            "<br> Device Modality : " + modality;
        document.getElementById(element).innerHTML = retutnVal;
    }, function (error) {
        showMessage(error.message);
    });
}

function onClear() {
    let vDiv = document.getElementById('imagediv');
    vDiv.innerHTML = "";
    localStorage.setItem("imageSrc", "");
    localStorage.setItem("wsq", "");
    localStorage.setItem("raw", "");
    localStorage.setItem("intermediate", "");
    disableEnableExport(true);
}

function toggle_visibility(ids) {
    document.getElementById("qualityInputBox").value = "";
    onStop();
    enableDisableScanQualityDiv(ids[0]);
    for (let i = 0; i < ids.length; i++) {
        let e = document.getElementById(ids[i]);
        if (i === 0) {
            e.style.display = 'block';
            state = e;
            disableEnable();
        } else {
            e.style.display = 'none';
        }
    }
}

// Ensure jQuery is loaded before using $
$(document).ready(function () {
    $("#save").on("click", function () {
        let imageSrc = localStorage.getItem("imageSrc");
        let memberId = $("#memberid").val(); // Make sure your input field uses id="member_id"

        if (!imageSrc || document.getElementById('imagediv').innerHTML.trim() === "") {
            alert("Error -> Fingerprint not available");
            return;
        }
        if (!memberId) {
            alert("Error -> Member ID not available");
            return;
        }

        $.ajax({
            url: '../../db/fingerprintConnection.php',
            type: 'POST',
            data: { user_id: memberId, fingerprint: imageSrc },
            success: function (response) {
                alert(response);
            },
            error: function (xhr, status, error) {
                alert("Error saving fingerprint to database: " + error);
            }
        });
    });
});

function populateReaders(readersArray) {
    let _deviceInfoTable = document.getElementById("deviceInfo");
    _deviceInfoTable.innerHTML = "";
    if (readersArray.length !== 0) {
        _deviceInfoTable.innerHTML += "<h4>Available Readers</h4>";
        for (let i = 0; i < readersArray.length; i++) {
            // Render a placeholder, then call onDeviceInfo to fill it asynchronously
            let readerId = readersArray[i];
            _deviceInfoTable.innerHTML +=
                "<div id='dynamicInfoDivs' align='left'>" +
                "<div data-toggle='collapse' data-target='#" + readerId + "'>" +
                "<img src='images/info.png' alt='Info' height='20' width='20'> &nbsp; &nbsp;" + readerId + "</div>" +
                "<p class='collapse' id='" + readerId + "'>Loading...</p>" +
                "</div>";
            onDeviceInfo(readerId, readerId);
        }
    }
}

function sampleAcquired(s) {
    if (currentFormat === Fingerprint.SampleFormat.PngImage) {
        localStorage.setItem("imageSrc", "");
        let samples = JSON.parse(s.samples);
        localStorage.setItem("imageSrc", "data:image/png;base64," + Fingerprint.b64UrlTo64(samples[0]));
        if (state === document.getElementById("content-capture")) {
            let vDiv = document.getElementById('imagediv');
            vDiv.innerHTML = "";
            let image = document.createElement("img");
            image.id = "image";
            image.src = localStorage.getItem("imageSrc");
            vDiv.appendChild(image);
        }
        disableEnableExport(false);
    } else if (currentFormat === Fingerprint.SampleFormat.Raw) {
        localStorage.setItem("raw", "");
        let samples = JSON.parse(s.samples);
        let sampleData = Fingerprint.b64UrlTo64(samples[0].Data);
        let decodedData = JSON.parse(Fingerprint.b64UrlToUtf8(sampleData));
        localStorage.setItem("raw", Fingerprint.b64UrlTo64(decodedData.Data));
        document.getElementById('imagediv').innerHTML = '<div id="animateText" style="display:none">RAW Sample Acquired <br>' + Date() + '</div>';
        setTimeout(() => delayAnimate("animateText", "table-cell"), 100);
        disableEnableExport(false);
    } else if (currentFormat === Fingerprint.SampleFormat.Compressed) {
        localStorage.setItem("wsq", "");
        let samples = JSON.parse(s.samples);
        let sampleData = Fingerprint.b64UrlTo64(samples[0].Data);
        let decodedData = JSON.parse(Fingerprint.b64UrlToUtf8(sampleData));
        localStorage.setItem("wsq", "data:application/octet-stream;base64," + Fingerprint.b64UrlTo64(decodedData.Data));
        document.getElementById('imagediv').innerHTML = '<div id="animateText" style="display:none">WSQ Sample Acquired <br>' + Date() + '</div>';
        setTimeout(() => delayAnimate("animateText", "table-cell"), 100);
        disableEnableExport(false);
    } else if (currentFormat === Fingerprint.SampleFormat.Intermediate) {
        localStorage.setItem("intermediate", "");
        let samples = JSON.parse(s.samples);
        let sampleData = Fingerprint.b64UrlTo64(samples[0].Data);
        localStorage.setItem("intermediate", sampleData);
        document.getElementById('imagediv').innerHTML = '<div id="animateText" style="display:none">Intermediate Sample Acquired <br>' + Date() + '</div>';
        setTimeout(() => delayAnimate("animateText", "table-cell"), 100);
        disableEnableExport(false);
    } else {
        alert("Format Error");
    }
}

function readersDropDownPopulate(checkForRedirecting) {
    myVal = "";
    let allReaders = test.getInfo();
    allReaders.then(function (sucessObj) {
        let readersDropDownElement = document.getElementById("readersDropDown");
        readersDropDownElement.innerHTML = "";
        let option = document.createElement("option");
        option.selected = "selected";
        option.value = "";
        option.text = "Select Reader";
        readersDropDownElement.add(option);
        for (let i = 0; i < sucessObj.length; i++) {
            let option = document.createElement("option");
            option.value = sucessObj[i];
            option.text = 'Digital Persona (' + sucessObj[i] + ')';
            readersDropDownElement.add(option);
        }
        checkReaderCount(sucessObj, checkForRedirecting);
    }, function (error) {
        showMessage(error.message);
    });
}

function checkReaderCount(sucessObj, checkForRedirecting) {
    if (sucessObj.length === 0) {
        alert("No reader detected. Please connect a reader.");
    } else if (sucessObj.length === 1) {
        document.getElementById("readersDropDown").selectedIndex = "1";
        if (checkForRedirecting) {
            toggle_visibility(['content-capture', 'content-reader']);
            enableDisableScanQualityDiv("content-capture");
            setActive('Capture', 'Reader');
        }
    }
    selectChangeEvent();
}

function selectChangeEvent() {
    let readersDropDownElement = document.getElementById("readersDropDown");
    myVal = readersDropDownElement.options[readersDropDownElement.selectedIndex].value;
    disableEnable();
    onClear();
    document.getElementById('imageGallery').innerHTML = "";
    if (myVal === "") {
        $('#capabilities').prop('disabled', true);
    } else {
        $('#capabilities').prop('disabled', false);
    }
}

function populatePopUpModal() {
    let modelWindowElement = document.getElementById("ReaderInformationFromDropDown");
    modelWindowElement.innerHTML = "";
    if (myVal !== "") {
        onDeviceInfo(myVal, "ReaderInformationFromDropDown");
    } else {
        modelWindowElement.innerHTML = "Please select a reader";
    }
}

function disableEnable() {
    if (myVal !== "") {
        disabled = false;
        $('#start').prop('disabled', false);
        $('#stop').prop('disabled', false);
        showMessage("");
        disableEnableStartStop();
    } else {
        disabled = true;
        $('#start').prop('disabled', true);
        $('#stop').prop('disabled', true);
        showMessage("Please select a reader");
        onStop();
    }
}

// Start-- Optional to make GUI user friendly
$('body').click(function () { disableEnableStartStop(); });

function disableEnableStartStop() {
    if (myVal !== "") {
        if (test.acquisitionStarted) {
            $('#start').prop('disabled', true);
            $('#stop').prop('disabled', false);
        } else {
            $('#start').prop('disabled', false);
            $('#stop').prop('disabled', true);
        }
    }
}

function enableDisableScanQualityDiv(id) {
    const scoresElem = document.getElementById('Scores');
    if (!scoresElem) return;
    if (id === "content-reader") {
        scoresElem.style.display = 'none';
    } else {
        scoresElem.style.display = 'block';
    }
}

function setActive(element1, element2) {
    document.getElementById(element2).className = "";
    document.getElementById(element1).className = "active";
}

// For Download and formats starts

function onImageDownload() {
    if (currentFormat === Fingerprint.SampleFormat.PngImage) {
        if (localStorage.getItem("imageSrc") === "" || localStorage.getItem("imageSrc") == null || document.getElementById('imagediv').innerHTML === "") {
            alert("No image to download");
        } else {
            downloadURI(localStorage.getItem("imageSrc"), "sampleImage.png", "image/png");
        }
    } else if (currentFormat === Fingerprint.SampleFormat.Compressed) {
        if (localStorage.getItem("wsq") === "" || localStorage.getItem("wsq") == null || document.getElementById('imagediv').innerHTML === "") {
            alert("WSQ data not available.");
        } else {
            downloadURI(localStorage.getItem("wsq"), "compressed.wsq", "application/octet-stream");
        }
    } else if (currentFormat === Fingerprint.SampleFormat.Raw) {
        if (localStorage.getItem("raw") === "" || localStorage.getItem("raw") == null || document.getElementById('imagediv').innerHTML === "") {
            alert("RAW data not available.");
        } else {
            downloadURI("data:application/octet-stream;base64," + localStorage.getItem("raw"), "rawImage.raw", "application/octet-stream");
        }
    } else if (currentFormat === Fingerprint.SampleFormat.Intermediate) {
        if (localStorage.getItem("intermediate") === "" || localStorage.getItem("intermediate") == null || document.getElementById('imagediv').innerHTML === "") {
            alert("Intermediate data not available.");
        } else {
            downloadURI("data:application/octet-stream;base64," + localStorage.getItem("intermediate"), "FeatureSet.bin", "application/octet-stream");
        }
    } else {
        alert("Nothing to download.");
    }
}

function downloadURI(uri, name, dataURIType) {
    if (IeVersionInfo() > 0) {
        let blob = dataURItoBlob(uri, dataURIType);
        window.navigator.msSaveOrOpenBlob(blob, name);
    } else {
        let save = document.createElement('a');
        save.href = uri;
        save.download = name;
        let event = document.createEvent("MouseEvents");
        event.initMouseEvent(
            "click", true, false, window, 0, 0, 0, 0, 0,
            false, false, false, false, 0, null
        );
        save.dispatchEvent(event);
    }
}

function dataURItoBlob(dataURI, dataURIType) {
    let binary = atob(dataURI.split(',')[1]);
    let array = [];
    for (let i = 0; i < binary.length; i++) {
        array.push(binary.charCodeAt(i));
    }
    return new Blob([new Uint8Array(array)], { type: dataURIType });
}

function IeVersionInfo() {
    let sAgent = window.navigator.userAgent;
    let IEVersion = sAgent.indexOf("MSIE");
    if (IEVersion > 0)
        return parseInt(sAgent.substring(IEVersion + 5, sAgent.indexOf(".", IEVersion)));
    else if (!!navigator.userAgent.match(/Trident\/7\./))
        return 11;
    else if (document.documentMode || /Edge/.test(navigator.userAgent))
        return 12;
    else
        return 0;
}

function checkOnly(stayChecked) {
    disableEnableExport(true);
    onClear();
    onStop();
    const myForm = document.myForm;
    if (myForm && myForm.elements) {
        for (let i = 0; i < myForm.elements.length; i++) {
            if (myForm.elements[i].checked === true && myForm.elements[i].name !== stayChecked.name) {
                myForm.elements[i].checked = false;
            }
        }
        for (let i = 0; i < myForm.elements.length; i++) {
            if (myForm.elements[i].checked === true) {
                if (myForm.elements[i].name === "PngImage") {
                    disableEnableSaveThumbnails(false);
                } else {
                    disableEnableSaveThumbnails(true);
                }
            }
        }
    }
}
function assignFormat() {
    currentFormat = "";
    const myForm = document.myForm;
    if (myForm && myForm.elements) {
        for (let i = 0; i < myForm.elements.length; i++) {
            if (myForm.elements[i].checked === true) {
                if (myForm.elements[i].name === "Raw") {
                    currentFormat = Fingerprint.SampleFormat.Raw;
                }
                if (myForm.elements[i].name === "Intermediate") {
                    currentFormat = Fingerprint.SampleFormat.Intermediate;
                }
                if (myForm.elements[i].name === "Compressed") {
                    currentFormat = Fingerprint.SampleFormat.Compressed;
                }
                if (myForm.elements[i].name === "PngImage") {
                    currentFormat = Fingerprint.SampleFormat.PngImage;
                }
            }
        }
    }
}


function disableEnableExport(val) {
    $('#saveImagePng').prop('disabled', val);
}

function disableEnableSaveThumbnails(val) {
    $('#save').prop('disabled', val);
}

function delayAnimate(id, visibility) {
    document.getElementById(id).style.display = visibility;
}