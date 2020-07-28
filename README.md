# Loxberry Plugin: Automower Connect API
Dieses Plugin ruft jede Minute den Status des Automowers ab und sendet die Daten über UDP an den gewählten Miniserver. Mittels virtuellem Ausgang kann der Miniserver die Befehle "park", "stop" und "start3h" an den Automower senden. Bei einer Steuerung des Automowers durch den Miniserver sollte der Zeitplan in der AMC App auf ein Minimum reduziert werden da es ansonsten zu konflikten zwischen dem Husqvarna Zeitplan und der Steuerung durch den Miniserver kommen kann...

Das Plugin verwendet die von der mobile APP "Automower Connect" genutzten API. Da diese API nicht offiziell dokumentiert ist kann Husqvarna an dieser API jederzeit Änderungen vornehmen die zu Funktionsstörungen des Plugins führen könnten.

## Feedback und Diskussion
Das PlugIn wird von mir noch weiterentwickelt und ich freue mich über Anregungen und Feedback. 

## Change-Log
- 2020-07-28  Erstellung PlugIn v 0.1.0

## Known-Issues
- 
