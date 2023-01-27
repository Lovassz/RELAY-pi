// Variables used by Scriptable.
// These must be at the very top of the file. Do not edit.
// icon-color: red; icon-glyph: magic;
const dataUrl = "http://Lovi:@192.168.1.5/index.php?status";

let widget = await createWidget();
Script.setWidget(widget);
widget.presentSmall();
Script.complete();

async function createWidget() {

    const widget = new ListWidget();

    let data = {
        "serverStatus": "ERROR",
        "switchStatus": "?",
        "relayStatus" : "?"
    };

    const req = new Request(dataUrl);
    req.allowInsecureRequest = true;
    try {

        data = await req.loadJSON();

    } catch(err) {}

    if (data.serverStatus === 'ERROR') {}
    else if (data.serverStatus + data.switchStatus + data.relayStatus !== 'OKOKOK') {
        let noti      = new Notification()
        noti.title    = "OUTGE!!!";
        noti.body     = "A RELAY egyik komponense nem válaszol!";
        noti.schedule();
    }

    let titleRow = widget.addText(`Ping status`);
    titleRow.font = Font.boldSystemFont(15);
    titleRow.textColor = Color.white();

    // Time
    let timeRow = widget.addText( ('0'+(new Date()).getHours()).substr(-2) +':'+ ('0'+(new Date()).getMinutes()).substr(-2) );
    timeRow.font = Font.boldSystemFont(10);
    timeRow.textColor = Color.white();

    widget.addSpacer(10);

    
    // Server
    let serwerRow = widget.addText(`Server ${data.serverStatus}`);
    serwerRow.font = Font.boldSystemFont(15);
    serwerRow.textColor = (data.serverStatus === 'OK') ? Color.green() : Color.red();

    
    // Switch
    let switchRow = widget.addText(`Switch ${data.switchStatus}`);
    switchRow.font = Font.boldSystemFont(15);
    switchRow.textColor = (data.switchStatus === 'OK') ? Color.green() : Color.red();

    
    // Relay
    let relayRow = widget.addText(`Relay ${data.relayStatus}`);
    relayRow.font = Font.boldSystemFont(15);
    relayRow.textColor = (data.relayStatus === 'OK') ? Color.green() : Color.red();

    let bgColor = new Color("#191d28", 1);
    
    widget.backgroundColor = bgColor
    return widget;
};