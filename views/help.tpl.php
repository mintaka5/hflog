<?php if($this->manager->isMode()): ?>
<div>
    <h2 id="the-manual">The Manual</h2>
    <h3 id="submit-a-log">Submit a log</h3>
    <div class="list-group">
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Frequency</h4>
            <p class="list-group-item-text">In kilohertz (kHz) and is <strong>required</strong>.</p>
        </div>
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Mode</h4>
            <p class="list-group-item-text">AM, USB, LSB, CW, etc. This field is&nbsp;<strong>required</strong></p>
        </div>
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Time on</h4>
            <p class="list-group-item-text">What time did you hear the broadcast? Be sure to check that it is UTC time. By default UTC time is supplied at page load. This field is <strong>required</strong>.</p>
        </div>
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Date</h4>
            <p class="list-group-item-text">What was the date when you heard the&nbsp;broadcast? This field is <strong>required</strong>.</p>
        </div>
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Station</h4>
            <p class="list-group-item-text">This is the broadcaster's station name/title. If you know what institution facilitates the log's broadcast select from this menu. This will prompt you to select the following menu field for Location.</p>
            <div class="well">
                <h5>Adding a new station</h5>
                <p>If there is no appropriate station available, or there is a new one, select the <strong>Add station</strong> button, and complete the pop-up form.</p>
            </div>
        </div>
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Location</h4>
            <p class="list-group-item-text">Every station has multiple transmitter locations. When you select a station, you are provided only locations for that station. If a station is selected, but a location is not selected, neither will be assigned to the log.</p>
            <div class="well">
                <h5>Adding a new location</h5>
                <p>
                    After selecting a station, and there is no appropriate location, select the <strong>Add location</strong> button, and complete the
                    pop-up form. If a location already exists, but has a different time, frequency, or language, simply start typing the
                    location's name, and select from the drop down menu under the <strong>Site</strong> text field. This will pre-populate
                    the location's latitude and longitude.
                </p>
            </div>
        </div>
        <div class="list-group-item">
            <h4 class="list-group-item-heading">Description</h4>
            <p class="list-group-item-text">A detailed account of what you heard. This field is <strong>required</strong>.</p>
        </div>
    </div>
    <h3 id="submitting-audio">Submitting audio</h3>
    <p>Due to storage limits, we cannot allow users to upload audio directly to their logs. In the meantime audio recordings of what you hear can be submitted for vetting through other means. <a href="https://www.dropbox.com/request/m9SBFOoCOoiXyNhg65pW" target="_blank">Using our Dropbox account</a>, users are able to upload audio there which we will then add to the logs.</p>
    <p>One you have your recording completed, rename the file to meet our naming standard, so that we can apply the file to the approriate log. Use this format:</p>
    <p class="well well-lg" style="padding-left: 30px;">&lt;<em>frequency in kHz</em>&gt;_&lt;<em>year</em>&gt;&lt;<em>month</em>&gt;&lt;<em>day</em>&gt;&lt;<em>hour</em>&gt;&lt;<em>minute</em>&gt;_&lt;<em>username</em>&gt;.&lt;<em>file</em> <em>extension</em>&gt;</p>
    <p>For example:</p>
    <p class="well-lg well" style="padding-left: 30px;"><strong>10057_2016062900006_jdoe.mp3</strong></p>
    <p>&nbsp;</p>
</div>
<?php endif;
