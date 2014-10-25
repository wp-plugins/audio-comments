/**
 * Below is the MP3Recorder JS API (Callbacks) for Audior ShorCode
 *
 * @category	Comments
 * @package		Audio Comments Plugin for Wordpress
 * @author		Radu Patron <radu@nusofthq.com>
 * @copyright	2014 Nusofthq.com
 * @license		This Wordpress Plugin is distributed under the terms of the GNU General Public License V2.0.
 * 				you can redistribute it and/or modify it under the terms of the GNU General Public License V2.0
 * 				as published by the Free Software Foundation, either version 3 of the License, or any later version.
 * 				You should have received a copy of the GNU General Public License along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
 * @link		http://www.audior.ec
 * @version		1.1
 */

function btSaveClick(recordDuration,recorderId){
	//this function is called when the SAVE button is released and it is called with 3 parameters: 
	//recordDuration: duration of the recorded audio file in seconds but accurate to the millisecond (like this: 4.322)
	//recorderId: the recorderId sent via flash vars, to be used when there are many recorders on the same web page
	//console.log("Audior.btSaveClick("+recordDuration+","+recorderId+")");
}

function btDownloadClick(recorderId){
	//this function is called when the DOWNLOAD button is released 
	//console.log("Audior.btDownloadClick("+recorderId+")");
}
function btPlayClick(recorderId){
	//this function is called when the PLAY button is released
	//console.log("Audior.btPlayClick("+recorderId+")");
}

function btPauseClick(recorderId){
	//this function is called when the PAUSE button is released
	//console.log("Audior.btPauseClick("+recorderId+")");
}

function btStopClick(recorderId){
	//this function is called when the STOP button is released
	//console.log("Audior.btStopClick("+recorderId+")");
}

function btRecordClick(recorderId){
	//this function is called when the RECORD button is released
	//console.log("Audior.btRecordClick("+recorderId+")");
}

function btPauseRecordingClick(recorderId){
	//this function is called when the Pause Recording button is released
	//console.log("Audior.btPauseRecordingClick("+recorderId+")");
}

function btResumeRecordingClick(recorderId){
	//this function is called when the Resume Recording button is released
	//console.log("Audior.btResumeRecordingClick("+recorderId+")");
}


function onRecordingPlaybackStopped(recorderId){
	//the recording has stopped playing
	//console.log('Audior.onRecordingPlaybackStopped('+recorderId+')')
}

function onEncodingDone(duration, recorderId){
	//the MP3Recorder has finished the encoding of your audio data to mp3
	//console.log('Audior.onEncodingDone('+duration+', '+recorderId+')')
}

function onUploadDone(success, recordName, duration, recorderId){
	//the MP3Recorder has finished uploading the files to your server AND it has received a save=ok OR save=failed response from the upload script on the web server
	//success  will be true if the upload succeeded and false otherwise
	//console.log('Audior.onUploadDone('+success+', '+recordName+', '+duration+', '+recorderId+')')
}

function onMicAccess(allowed,recorderId){
	//console.log("Audior.onMicAccess("+allowed+","+recorderId+")");
	//the user clicked Allow or Deny in the Privacy panel dialog box in Flash Player
	//when the user clicks Deny this function is called with allowed=false
	//when the user clicks Allow this function is called with allowed=true
	//this function can be called anytime during the life of the Audior instance as the user has permanent access to the Privacy panel dialog box in Flash Player
}

function onFlashReady(recorderId){
	//console.log("Audior.onFlashReady("+recorderId+")");
	//as soon as this function is called you can communicate with Audior using the JS Control API
	//Example : document.Audior.record(); will make a call to flash in order to start recording
	//recorderId: the recorderId sent via flash vars, to be used when there are many recorders on the same web page
	
}