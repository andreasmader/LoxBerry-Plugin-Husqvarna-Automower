# Loxberry Plugin: Automower Connect API
Dieses Plugin ruft jede Minute den Status des Automowers ab und sendet die Daten über UDP an den gewählten Miniserver. Mittels virtuellem Ausgang kann der Miniserver die folgenden Befehle:

    "pause":                 Automower am aktuellen Standort pausieren
    "start":                 Starte den Automower gemäss Zeitplan
    "start3h":               Starte den Automower und ignoriere den Zeitplan für die nächsten 3 Stunden
    "start6h":               Starte den Automower und ignoriere den Zeitplan für die nächsten 6 Stunden
    "start12h":              Starte den Automower und ignoriere den Zeitplan für die nächsten 12 Stunden
    "park":                  Schicke den Automower in die Ladestation (bis auf weiteres)
    "parkuntilnextschedule": Schicke den Automower in die Ladestation (mit Zeitplan starten)
    "park3h":                Schicke den Automower in die Ladestation (Dauer 3 Stunden)
    "park6h":                Schicke den Automower in die Ladestation (Dauer 6 Stunden)
    "park12h":               Schicke den Automower in die Ladestation (Dauer 12 Stunden)
    "cuttingHeight:xx":      Schnitthöhe auf xx einstellen (xx= integer)
    "ecoMode:x":             x=0 ecoMode ausschalten; x=1 ecoMode einschalten

Bei einer Steuerung des Automowers durch den Miniserver sollte der Zeitplan in der AMC App auf ein Minimum reduziert werden da es ansonsten zu konflikten zwischen dem Husqvarna Zeitplan und der Steuerung durch den Miniserver kommen kann...

Das Plugin verwendet die von der mobile APP "Automower Connect" genutzten API. Da diese API nicht offiziell dokumentiert ist kann Husqvarna an dieser API jederzeit Änderungen vornehmen die zu Funktionsstörungen des Plugins führen könnten.

Der UDP Port kann in den PlugIn Settings gewählt werden.
Jede Minute sendet das Plugin einen String an den Miniserver im Format {"activitynum":3,"statenum":3,"batteryPercent":93,"interval":26, "timestamp":369332293,"nextStart":369401455,"lastchargingtime":2500}

activitynum:

    0:  Unbekannt
    1:  N/A
    2:  Mähen
    3:  Automower fährt Richtung Ladestation
    4:  Laden
    5:  Automower verlässt Ladestation
    6:  Automower geparkt in Ladestation

statenum:

    0:      Unbekannt
    1:      N/A
    2:      Automower Pause
    3:      Automower im Betrieb
    4:      Warten - Update läuft
    5:      Warten - Power-Up
    6:      Eingeschränkt
    7:      Automower AUS
    8:      Automower gestoppt - Manueller Eingriff nötig
    
    10-100: Fehler (übertragen wird 10+ Automower Error-Code)

        10  Unexpected error
        11  Outside working area
        12  No loop signal
        13  Wrong loop signal
        14  Loop sensor problem, front
        15  Loop sensor problem, rear
        16  Loop sensor problem, left
        17  Loop sensor problem, right
        18  Wrong PIN code
        19  Trapped
        20  Upside down
        21  Low battery
        22  Empty battery
        23  No drive
        24  Mower lifted
        25  Lifted
        26  Stuck in charging station
        27  Charging station blocked
        28  Collision sensor problem, rear
        29  Collision sensor problem, front
        30  Wheel motor blocked, right
        31  Wheel motor blocked, left
        32  Wheel drive problem, right
        33  Wheel drive problem, left
        34  Cutting system blocked
        35  Cutting system blocked
        36  Invalid sub-device combination
        37  Settings restored
        38  Memory circuit problem
        39  Slope too steep
        40  Charging system problem
        41  STOP button problem
        42  Tilt sensor problem
        43  Mower tilted
        44  Cutting stopped - slope too steep
        45  Wheel motor overloaded, right
        46  Wheel motor overloaded, left
        47  Charging current too high
        48  Electronic problem
        49  Cutting motor problem
        50  Limited cutting height range
        51  Unexpected cutting height adj
        52  Limited cutting height range
        53  Cutting height problem, drive
        54  Cutting height problem, curr
        55  Cutting height problem, dir
        56  Cutting height blocked
        57  Cutting height problem
        58  No response from charger
        59  Ultrasonic problem
        60  Guide 1 not found
        61  Guide 2 not found
        62  Guide 3 not found
        63  GPS navigation problem
        64  Weak GPS signal
        65  Difficult finding home
        66  Guide calibration accomplished
        67  Guide calibration failed
        68  Temporary battery problem
        69  Temporary battery problem
        70  Temporary battery problem
        71  Temporary battery problem
        72  Temporary battery problem
        73  Temporary battery problem
        74  Temporary battery problem
        75  Temporary battery problem
        76  Battery problem
        77  Battery problem
        78  Temporary battery problem
        79  Alarm! Mower switched off
        80  Alarm! Mower stopped
        81  Alarm! Mower lifted
        82  Alarm! Mower tilted
        83  Alarm! Mower in motion
        84  Alarm! Outside geofence
        85  Connection changed
        86  Connection NOT changed
        87  Com board not available
        88  Slipped - Mower has Slipped.Situation not solved with moving pattern
        89  Invalid battery combination - Invalid combination of different battery types.
        90  Cutting system imbalance    Warning
        91  Safety function faulty
        92  Wheel motor blocked, rear right
        93  Wheel motor blocked, rear left
        94  Wheel drive problem, rear right
        95  Wheel drive problem, rear left
        96  Wheel motor overloaded, rear right
        97  Wheel motor overloaded, rear left
        98  Angular sensor problem
        99  Invalid system configuration
        100 No power in charging station

batteryPercent:
    Ladezustand des Akkus (0-100%)
    
interval:
    Dies entspricht der Zeit seit der letzten aktiven Datenübertragunbg in Sekunden. Dieser Wert dient dazu festzustellen ob die Kommuniation mit der API ordenbtlich funktioniert (Wert im Test bei normalem Betrieb stets kleiner als 300)

timestamp:
	Zeitstempel der letzten Kommunikation mit dem Automower - gemeldet vom Plugin (korrigiert auf Loxone Zeitformat)

nextStart:
	Geplanter Start des Automowers gemäss Husqvarna Zeitplanung
	
lastchargingtime:
	Dauer der letzten ununterbrochenen Ladung des Akkus (Extrapolierte Zeit in Sekunden für Ladung von 0% auf 100%)

## Feedback und Diskussion
Das PlugIn wird von mir noch weiterentwickelt und ich freue mich über Anregungen und Feedback.

## Change-Log
- 2020-08-02  Erstellung PlugIn HAC_v1.0.2
- 2020-10-26  PlugIn pre-release HAC_v1.0.3
			  Erweiterung des Plugins mit der Erfassung der Akku-Ladezeit zwischen 45% und 80% Ladezustand
			  Implementierung der Ausgabe der nächsten Startzeit gemäss Husqvarna Zeitplanung
			  Befehl "start" (Start mit Zeitplan) eingefügt
- 2020-10-27  PlugIn pre-release HAC_v1.0.4
			  Befehl "start3h"  Start (Zeitplan aus für 3 Stunden)
			  Befehl "start6h"  Start (Zeitplan aus für 6 Stunden)
			  Befehl "start12h" Start (Zeitplan aus für 12 Stunden)
			  Befehl "parkuntilnextschedule" Automower Parken (mit Zeitplan starten)
			  Befehl "park3h"  Automower Parken (Dauer 3 Stunden)
			  Befehl "park6h"  Automower Parken (Dauer 6 Stunden)	 
			  Befehl "park12h" Automower Parken (Dauer 12 Stunden)
			  Befehl "cuttingHeight:xx" implementiert (xx= integer)
			  Befehl "ecoMode:x" implementiert (x= 0 ecoMode aus; x= 1 ecoMode ein)
## Known-Issues
- 
