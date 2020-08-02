# Loxberry Plugin: Automower Connect API
Dieses Plugin ruft jede Minute den Status des Automowers ab und sendet die Daten über UDP an den gewählten Miniserver. Mittels virtuellem Ausgang kann der Miniserver die Befehle "park", "stop" und "start3h" an den Automower senden. Bei einer Steuerung des Automowers durch den Miniserver sollte der Zeitplan in der AMC App auf ein Minimum reduziert werden da es ansonsten zu konflikten zwischen dem Husqvarna Zeitplan und der Steuerung durch den Miniserver kommen kann...

Das Plugin verwendet die von der mobile APP "Automower Connect" genutzten API. Da diese API nicht offiziell dokumentiert ist kann Husqvarna an dieser API jederzeit Änderungen vornehmen die zu Funktionsstörungen des Plugins führen könnten.

Der UDP Port kann in den PlugIn Settings gewählt werden.
Jede Minute sendet das Plugin einen String an den Miniserver im Format {"activitynum":3,"statenum":3,"batteryPercent":93,"interval":26}

activitynum:

    0:  Unbekannt
    1:  N/A
    2:  Mähen
    3:  Automower fährt Richtung Ladestation
    4:  Laden
    5:  Automower verlässt Ladestation
    6:  Automower in Ladestation geparkt
    
statenum:
    0:  Unbekannt
    1:  N/A
    2:  Automower Pause
    3:  Automower im Betrieb
    4:  Warten - Update läuft
    5:  Warten - Power-Up
    6:  Eingeschränkt
    7:  Automower AUS
    8:  Automower gestoppt
    10: Error (übertragen wird 10+ Automower Error-Code)
	11: Fehler - Automower ausserhalb der Begrenzung
        12: ??
        13: ??
        14: ??
        15: ??
        16: ??
        17: ??
        18: ??
        19: Fehler - Automower eingeschlossen
batteryPercent:
    Ladezustand des Akkus (0-100%)
interval:
    Dies entspricht der Zeit seit der letzten aktiven Datenübertragunbg in Sekunden. Dieser Wert dient dazu festzustellen ob die Kommuniation mit der API ordenbtlich funktioniert (Wert im Test bei normalem Betrieb stets kleiner als 300)

## Feedback und Diskussion
Das PlugIn wird von mir noch weiterentwickelt und ich freue mich über Anregungen und Feedback.

## Change-Log
- 2020-08-02  Erstellung PlugIn HAC_v1.0.0

## Known-Issues
- 
