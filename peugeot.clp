(clear)
(reset)
(bind ?*zmienna* 0)
	
;;interfejs
(printout t "************************************" crlf)
(printout t "** 				  **" crlf)
(printout t "** System ekspertowy wspomagajacy **" crlf)
(printout t "** wybor samochodu Peugeot        **" crlf)
(printout t "** 				  **" crlf)
(printout t "************************************" crlf)
(printout t "************************************" crlf)
(printout t "** 				  **" crlf)
(printout t "** System zadaje kilka pytan      **" crlf)
(printout t "** i stara sie dobrac samochod    **" crlf)
(printout t "** 				  **" crlf)

(printout t "************************************" crlf)
(printout t " " crlf)
(printout t " " crlf)

;;pytania o fakty
(deftemplate carType (slot type))
(deftemplate carSize (slot size))
(deftemplate enginePower (slot power))
(deftemplate engineEconomy (slot economy))
(deftemplate carPrice (slot price))

(printout t "Wybierz rodzaj samochodu " crlf)
(printout t " " crlf)
(printout t "(kompaktowy, rodzinny, sportowy) " crlf)
(printout t " " crlf)
(bind ?a (read))
(assert (carType(type ?a)))

(printout t " " crlf)
(printout t "Wybierz wielkosc samochodu " crlf)
(printout t " " crlf)
(printout t "(maly, duzy) " crlf)
(printout t " " crlf)
(bind ?b (read))
(assert (carSize(size ?b)))

(printout t " " crlf)
(printout t "Podaj wymagana moc silnika " crlf)
(printout t " " crlf)
(printout t "(60-280 KM) " crlf)
(printout t " " crlf)
(bind ?c (read))
(assert (enginePower(power ?c)))

(printout t " " crlf)
(printout t "Jakie spalanie akceptujesz? " crlf)
(printout t " " crlf)
(printout t "(4,5-9 l/100km) " crlf)
(printout t " " crlf)
(bind ?d (read))
(assert (engineEconomy(economy ?d)))

(printout t " " crlf)
(printout t "Podaj maksymalna cene " crlf)
(printout t " " crlf)
(printout t "(38000-158000 z³) " crlf)
(printout t " " crlf)
(bind ?e (read))
(assert (carPrice(price ?e)))

(printout t " " crlf)
(printout t "Udzielono nastepujacych odpowiedzi: " crlf)
(printout t " " crlf)
(printout t "Rodzaj: " ?a crlf)
(printout t " " crlf)
(printout t "Wielkosc: " ?b crlf)
(printout t " " crlf)
(printout t "Moc: " ?c crlf)
(printout t " " crlf)
(printout t "Spalanie: " ?d crlf)
(printout t " " crlf)
(printout t "Cena: " ?d crlf)
(printout t " " crlf)

(printout t " " crlf)
(printout t "Na podstawie zebranych danych " crlf)
(printout t " " crlf)
(printout t "System ekspertowy proponuje: " crlf)


;;funkcje
(deffunction car1 ()
(printout t " " crlf)
(printout t "carefon ma rowniez funkcje: " crlf
  " - wybieranie jednoprzyciskowe " crlf
  " - bateria wibracyjna " crlf
  " - kalkulator " crlf
  " - budzik, zegarek " crlf
  " - zapamietywanie adresu " crlf
  " - klips "  crlf) 

)




;;reguly
(defrule rule1
    (carType {type == kompaktowy})
	(carSize {size == maly})
	(enginePower {power >= 60 && power <= 80})
	(engineEconomy {economy > 4 && economy <= 5})
	(carPrice {price > 38000 && price <= 43000})
=>
(printout t " " crlf)
(printout t "Peugeot 208 1.0 68KM Access 4,5 l/100km 40000zl " crlf)

(bind ?*zmienna* 1)
)




;;uruchamianie
;;(facts)
(run)

(if (= ?*zmienna* 0) then
(printout t " " crlf
"Niestety dla wybranych przez Pana/Pania parametrow " crlf 
" " crlf
"system ekspertowy nie znalazl odpowieniego carefonu " crlf
" " crlf
"prosze ponownie uruchomic system i wprowadzic inne parametry " crlf))




