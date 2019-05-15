<?php



//Query

$query="SELECT
		Fecha,
		SUM(VolumenTotal) as VolumenTotal,
		SUM(AnsweredTotal) as AnsweredTotal,
		SUM(AbandonedTotal) as AbandonedTotal,
		SUM(TransferidasTotal) as TransferidasTotal,
		SUM(TransferidasMinTotal) as TransferidasMinTotal,
		SUM(SLA20CallsTotal)/SUM(VolumenTotal) as SLA20Total,
		SUM(SLA30CallsTotal)/SUM(VolumenTotal) as SLA30Total,
		SUM(AbandonedTotal)/SUM(VolumenTotal) as AbandonTotal,
		SUM(TalkingTimeTotal)/SUM(AnsweredTotal) as AHTTotal,
		SUM(WaitingTimeTotal)/SUM(AnsweredTotal) as ASATotal,
		SUM(TalkingTimeTotal) as TalkingTimeTotal,
		SUM(VolumenMP) as VolumenMP,
		SUM(AnsweredMP) as AnsweredMP,
		SUM(AbandonedMP) as AbandonedMP,
		SUM(TransferidasMP) as TransferidasMP,
		SUM(TransferidasMinMP) as TransferidasMinMP,
		SUM(SLA20CallsMP)/SUM(VolumenMP) as SLA20MP,
		SUM(SLA30CallsMP)/SUM(VolumenMP) as SLA30MP,
		SUM(AbandonedMP)/SUM(VolumenMP) as AbandonMP,
		SUM(TalkingTimeMP)/SUM(AnsweredMP) as AHTMP,
		SUM(WaitingTimeMP)/SUM(AnsweredMP) as ASAMP,
		SUM(TalkingTimeMP) as TalkingTimeMP,
		SUM(VolumenIT) as VolumenIT,
		SUM(AnsweredIT) as AnsweredIT,
		SUM(AbandonedIT) as AbandonedIT,
		SUM(TransferidasIT) as TransferidasIT,
		SUM(TransferidasMinIT) as TransferidasMinIT,
		SUM(SLA20CallsIT)/SUM(VolumenIT) as SLA20IT,
		SUM(SLA30CallsIT)/SUM(VolumenIT) as SLA30IT,
		SUM(AbandonedIT)/SUM(VolumenIT) as AbandonIT,
		SUM(TalkingTimeIT)/SUM(AnsweredIT) as AHTIT,
		SUM(WaitingTimeIT)/SUM(AnsweredIT) as ASAIT,
		SUM(TalkingTimeIT) as TalkingTimeIT,
		SUM(VolumenCOPA) as VolumenCOPA,
		SUM(AnsweredCOPA) as AnsweredCOPA,
		SUM(AbandonedCOPA) as AbandonedCOPA,
		SUM(TransferidasCOPA) as TransferidasCOPA,
		SUM(TransferidasMinCOPA) as TransferidasMinCOPA,
		SUM(SLA20CallsCOPA)/SUM(VolumenCOPA) as SLA20COPA,
		SUM(SLA30CallsCOPA)/SUM(VolumenCOPA) as SLA30COPA,
		SUM(AbandonedCOPA)/SUM(VolumenCOPA) as AbandonCOPA,
		SUM(TalkingTimeCOPA)/SUM(AnsweredCOPA) as AHTCOPA,
		SUM(WaitingTimeCOPA)/SUM(AnsweredCOPA) as ASACOPA,
		SUM(TalkingTimeCOPA) as TalkingTimeCOPA,
		SUM(VolumenCOOMEVA) as VolumenCOOMEVA,
		SUM(AnsweredCOOMEVA) as AnsweredCOOMEVA,
		SUM(AbandonedCOOMEVA) as AbandonedCOOMEVA,
		SUM(TransferidasCOOMEVA) as TransferidasCOOMEVA,
		SUM(TransferidasMinCOOMEVA) as TransferidasMinCOOMEVA,
		SUM(SLA20CallsCOOMEVA)/SUM(VolumenCOOMEVA) as SLA20COOMEVA,
		SUM(SLA30CallsCOOMEVA)/SUM(VolumenCOOMEVA) as SLA30COOMEVA,
		SUM(AbandonedCOOMEVA)/SUM(VolumenCOOMEVA) as AbandonCOOMEVA,
		SUM(TalkingTimeCOOMEVA)/SUM(AnsweredCOOMEVA) as AHTCOOMEVA,
		SUM(WaitingTimeCOOMEVA)/SUM(AnsweredCOOMEVA) as ASACOOMEVA,
		SUM(TalkingTimeCOOMEVA) as TalkingTimeCOOMEVA,
		SUM(VolumenVECI) as VolumenVECI,
		SUM(AnsweredVECI) as AnsweredVECI,
		SUM(AbandonedVECI) as AbandonedVECI,
		SUM(TransferidasVECI) as TransferidasVECI,
		SUM(TransferidasMinVECI) as TransferidasMinVECI,
		SUM(SLA20CallsVECI)/SUM(VolumenVECI) as SLA20VECI,
		SUM(SLA30CallsVECI)/SUM(VolumenVECI) as SLA30VECI,
		SUM(AbandonedVECI)/SUM(VolumenVECI) as AbandonVECI,
		SUM(TalkingTimeVECI)/SUM(AnsweredVECI) as AHTVECI,
		SUM(WaitingTimeVECI)/SUM(AnsweredVECI) as ASAVECI,
		SUM(TalkingTimeVECI) as TalkingTimeVECI,
        SUM(VolumenLiverpool) as VolumenLiverpool,
		SUM(AnsweredLiverpool) as AnsweredLiverpool,
		SUM(AbandonedLiverpool) as AbandonedLiverpool,
		SUM(TransferidasLiverpool) as TransferidasLiverpool,
		SUM(TransferidasMinLiverpool) as TransferidasMinLiverpool,
		SUM(SLA20CallsLiverpool)/SUM(VolumenLiverpool) as SLA20Liverpool,
		SUM(SLA30CallsLiverpool)/SUM(VolumenLiverpool) as SLA30Liverpool,
		SUM(AbandonedLiverpool)/SUM(VolumenLiverpool) as AbandonLiverpool,
		SUM(TalkingTimeLiverpool)/SUM(AnsweredLiverpool) as AHTLiverpool,
		SUM(WaitingTimeLiverpool)/SUM(AnsweredLiverpool) as ASALiverpool,
		SUM(TalkingTimeLiverpool) as TalkingTimeLiverpool,
		SUM(LocalizadoresTotal)/(SUM(AnsweredTotal)-SUM(TransferidasMinTotal)) as FCTotal,
		SUM(LocalizadoresTotal) as LocalizadoresTotal,
		SUM(MontoTotal)*Dolar as MontoTotal,
		SUM(LocalizadoresMP)/(SUM(AnsweredMP)-SUM(TransferidasMinMP)) as FCMP,
		SUM(LocalizadoresMP) as LocalizadoresMP,
		SUM(MontoMP)*Dolar as MontoMP,
		SUM(LocalizadoresIT)/(SUM(AnsweredIT)-SUM(TransferidasMinIT)) as FCIT,
		SUM(LocalizadoresIT) as LocalizadoresIT,
		SUM(MontoIT)*Dolar as MontoIT,
		SUM(LocalizadoresCOPA)/(SUM(AnsweredCOPA)-SUM(TransferidasMinCOPA)) as FCCOPA,
		SUM(LocalizadoresCOPA) as LocalizadoresCOPA,
		SUM(MontoCOPA)*Dolar as MontoCOPA,
		SUM(LocalizadoresCOOMEVA)/(SUM(AnsweredCOOMEVA)-SUM(TransferidasMinCOOMEVA)) as FCCOOMEVA,
		SUM(LocalizadoresCOOMEVA) as LocalizadoresCOOMEVA,
		SUM(MontoCOOMEVA)*Dolar as MontoCOOMEVA,
		SUM(LocalizadoresVECI)/(SUM(AnsweredVECI)-SUM(TransferidasMinVECI)) as FCVECI,
		SUM(LocalizadoresVECI) as LocalizadoresVECI,
		SUM(MontoVECI)*Dolar as MontoVECI,
		SUM(LocalizadoresLiverpool)/(SUM(AnsweredLiverpool)-SUM(TransferidasMinLiverpool)) as FCLiverpool,
		SUM(LocalizadoresLiverpool) as LocalizadoresLiverpool,
		SUM(MontoLiverpool)*Dolar as MontoLiverpool,
		1-SUM(PNP)/SUM(DuracionSesiones) as UtilizacionTotal,
		(SUM(TalkingTimeTotal)+SUM(PP))/(SUM(DuracionSesiones)-SUM(PNP)) as OcupacionTotal,
		SUM(AsesoresPrimerNivel) as AsesoresPrimerNivelTotal,
		SUM(AsesoresApoyo) as AsesoresApoyoTotal,
		Dolar as TipoDeCambio
	FROM
		(
			SELECT
				Fecha, Dolar
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) D_Fechas
	LEFT JOIN
		(
			SELECT
				a.Fecha as Telefonia_Fecha, Skill,
				COUNT(ac_id) as VolumenTotal,
				COUNT(IF(Answered=1,ac_id,NULL)) as AnsweredTotal,
				COUNT(IF(Answered=0,ac_id,NULL)) as AbandonedTotal,
				COUNT(IF(Desconexion='Transferida',ac_id,NULL)) as TransferidasTotal,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30',ac_id,NULL)) as TransferidasMinTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) as SLA20CallsTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:30',ac_id,NULL)) as SLA30CallsTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Espera),0)) as WaitingTimeTotal,
				COUNT(IF(Canal LIKE '%MP MX%',ac_id,NULL)) as VolumenMP,
				COUNT(IF(Answered=1 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AnsweredMP,
				COUNT(IF(Answered=0 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AbandonedMP,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMP,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMinMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA20CallsMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA30CallsMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Espera),0)) as WaitingTimeMP,
				COUNT(IF(Canal LIKE '%Intertours%',ac_id,NULL)) as VolumenIT,
				COUNT(IF(Answered=1 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AnsweredIT,
				COUNT(IF(Answered=0 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AbandonedIT,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasIT,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasMinIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA20CallsIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA30CallsIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Espera),0)) as WaitingTimeIT,
				COUNT(IF(Canal LIKE '%copa%',ac_id,NULL)) as VolumenCOPA,
				COUNT(IF(Answered=1 AND Canal LIKE '%copa%',ac_id,NULL)) as AnsweredCOPA,
				COUNT(IF(Answered=0 AND Canal LIKE '%copa%',ac_id,NULL)) as AbandonedCOPA,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasCOPA,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasMinCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA20CallsCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA30CallsCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Espera),0)) as WaitingTimeCOPA,
				COUNT(IF(Canal LIKE '%coomeva%',ac_id,NULL)) as VolumenCOOMEVA,
				COUNT(IF(Answered=1 AND Canal LIKE '%coomeva%',ac_id,NULL)) as AnsweredCOOMEVA,
				COUNT(IF(Answered=0 AND Canal LIKE '%coomeva%',ac_id,NULL)) as AbandonedCOOMEVA,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%coomeva%',ac_id,NULL)) as TransferidasCOOMEVA,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%coomeva%',ac_id,NULL)) as TransferidasMinCOOMEVA,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%coomeva%',ac_id,NULL)) as SLA20CallsCOOMEVA,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%coomeva%',ac_id,NULL)) as SLA30CallsCOOMEVA,
				SUM(IF(Answered=1 AND Canal LIKE '%coomeva%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeCOOMEVA,
				SUM(IF(Answered=1 AND Canal LIKE '%coomeva%', TIME_TO_SEC(Espera),0)) as WaitingTimeCOOMEVA,
				COUNT(IF(Canal LIKE '%veci%',ac_id,NULL)) as VolumenVECI,
				COUNT(IF(Answered=1 AND Canal LIKE '%veci%',ac_id,NULL)) as AnsweredVECI,
				COUNT(IF(Answered=0 AND Canal LIKE '%veci%',ac_id,NULL)) as AbandonedVECI,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%veci%',ac_id,NULL)) as TransferidasVECI,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%veci%',ac_id,NULL)) as TransferidasMinVECI,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%veci%',ac_id,NULL)) as SLA20CallsVECI,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%veci%',ac_id,NULL)) as SLA30CallsVECI,
				SUM(IF(Answered=1 AND Canal LIKE '%veci%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeVECI,
				SUM(IF(Answered=1 AND Canal LIKE '%veci%', TIME_TO_SEC(Espera),0)) as WaitingTimeVECI,
                COUNT(IF(Canal LIKE '%liverpool%',ac_id,NULL)) as VolumenLiverpool,
				COUNT(IF(Answered=1 AND Canal LIKE '%liverpool%',ac_id,NULL)) as AnsweredLiverpool,
				COUNT(IF(Answered=0 AND Canal LIKE '%liverpool%',ac_id,NULL)) as AbandonedLiverpool,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%liverpool%',ac_id,NULL)) as TransferidasLiverpool,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%liverpool%',ac_id,NULL)) as TransferidasMinLiverpool,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%liverpool%',ac_id,NULL)) as SLA20CallsLiverpool,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%liverpool%',ac_id,NULL)) as SLA30CallsLiverpool,
				SUM(IF(Answered=1 AND Canal LIKE '%liverpool%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeLiverpool,
				SUM(IF(Answered=1 AND Canal LIKE '%liverpool%', TIME_TO_SEC(Espera),0)) as WaitingTimeLiverpool,
				COUNT(DISTINCT IF(`id Departamento`=$dept,asesor,NULL)) as AsesoresPrimerNivel,
				COUNT(DISTINCT IF(`id Departamento`!=$dept or `id Departamento` IS NULL,asesor,NULL)) as AsesoresApoyo
			FROM
				t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			LEFT JOIN
				Asesores c
			ON
				a.asesor=c.id
			WHERE
				Skill=$dept AND
				a.Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				a.Fecha
		) Telefonia
	ON
		Fecha=Telefonia_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Localizadores_Fecha,
				COUNT(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresTotal,
				SUM(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoTotal,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresMP,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoMP,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%intertours%'),localizador,NULL)) as LocalizadoresIT,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%intertours%'),Monto,NULL)) as MontoIT,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%copa%'),localizador,NULL)) as LocalizadoresCOPA,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%copa%'),Monto,NULL)) as MontoCOPA,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%coomeva%'),localizador,NULL)) as LocalizadoresCOOMEVA,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%coomeva%'),Monto,NULL)) as MontoCOOMEVA,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%corteingles%'),localizador,NULL)) as LocalizadoresVECI,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%corteingles%'),Monto,NULL)) as MontoVECI,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%liverpool%'),localizador,NULL)) as LocalizadoresLiverpool,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%liverpool%'),Monto,NULL)) as MontoLiverpool


			FROM
				(
					SELECT
						Fecha, Localizador, asesor, `id Departamento`, Afiliado, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta
					FROM
							t_Locs a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						a.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						a.Fecha, localizador
					HAVING
						Monto!=0 AND
						Venta!=0
				) Locs
			GROUP BY
				Fecha
		) Localizadores
	ON
		Fecha=Localizadores_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Pausas_Fecha,
				SUM(IF(Skill=$dept AND Productiva=0,TIME_TO_SEC(Duracion),0)) as PNP,
				SUM(IF(Skill=$dept AND Productiva=1,TIME_TO_SEC(Duracion),0)) as PP
			FROM
				t_pausas a
			LEFT JOIN
				Tipos_pausas b
			ON
				a.codigo=b.pausa_id
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) Pausas
	ON
		Fecha=Pausas_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha,
				SUM(IF(Skill=$dept,TIME_TO_SEC(Duracion),0)) as DuracionSesiones
			FROM
				t_Sesiones
			WHERE
				Fecha_in BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha_in
		) Sesiones
	ON
		Fecha=Sesiones_Fecha
	GROUP BY
		Fecha
    ORDER BY
        Fecha";



$query_ac="SELECT
		Fecha,
		SUM(VolumenTotal) as VolumenTotal,
		SUM(AnsweredTotal) as AnsweredTotal,
		SUM(AbandonedTotal) as AbandonedTotal,
		SUM(TransferidasTotal) as TransferidasTotal,
		SUM(TransferidasMinTotal) as TransferidasMinTotal,
		SUM(SLA20CallsTotal)/SUM(VolumenTotal) as SLA20Total,
		SUM(SLA30CallsTotal)/SUM(VolumenTotal) as SLA30Total,
		SUM(AbandonedTotal)/SUM(VolumenTotal) as AbandonTotal,
		SUM(TalkingTimeTotal)/SUM(AnsweredTotal) as AHTTotal,
		SUM(WaitingTimeTotal)/SUM(AnsweredTotal) as ASATotal,
		SUM(TalkingTimeTotal) as TalkingTimeTotal,
		SUM(VolumenMP) as VolumenMP,
		SUM(AnsweredMP) as AnsweredMP,
		SUM(AbandonedMP) as AbandonedMP,
		SUM(TransferidasMP) as TransferidasMP,
		SUM(TransferidasMinMP) as TransferidasMinMP,
		SUM(SLA20CallsMP)/SUM(VolumenMP) as SLA20MP,
		SUM(SLA30CallsMP)/SUM(VolumenMP) as SLA30MP,
		SUM(AbandonedMP)/SUM(VolumenMP) as AbandonMP,
		SUM(TalkingTimeMP)/SUM(AnsweredMP) as AHTMP,
		SUM(WaitingTimeMP)/SUM(AnsweredMP) as ASAMP,
		SUM(TalkingTimeMP) as TalkingTimeMP,
		SUM(VolumenIT) as VolumenIT,
		SUM(AnsweredIT) as AnsweredIT,
		SUM(AbandonedIT) as AbandonedIT,
		SUM(TransferidasIT) as TransferidasIT,
		SUM(TransferidasMinIT) as TransferidasMinIT,
		SUM(SLA20CallsIT)/SUM(VolumenIT) as SLA20IT,
		SUM(SLA30CallsIT)/SUM(VolumenIT) as SLA30IT,
		SUM(AbandonedIT)/SUM(VolumenIT) as AbandonIT,
		SUM(TalkingTimeIT)/SUM(AnsweredIT) as AHTIT,
		SUM(WaitingTimeIT)/SUM(AnsweredIT) as ASAIT,
		SUM(TalkingTimeIT) as TalkingTimeIT,
		SUM(VolumenCOPA) as VolumenCOPA,
		SUM(AnsweredCOPA) as AnsweredCOPA,
		SUM(AbandonedCOPA) as AbandonedCOPA,
		SUM(TransferidasCOPA) as TransferidasCOPA,
		SUM(TransferidasMinCOPA) as TransferidasMinCOPA,
		SUM(SLA20CallsCOPA)/SUM(VolumenCOPA) as SLA20COPA,
		SUM(SLA30CallsCOPA)/SUM(VolumenCOPA) as SLA30COPA,
		SUM(AbandonedCOPA)/SUM(VolumenCOPA) as AbandonCOPA,
		SUM(TalkingTimeCOPA)/SUM(AnsweredCOPA) as AHTCOPA,
		SUM(WaitingTimeCOPA)/SUM(AnsweredCOPA) as ASACOPA,
		SUM(TalkingTimeCOPA) as TalkingTimeCOPA,
		SUM(VolumenCOOMEVA) as VolumenCOOMEVA,
		SUM(AnsweredCOOMEVA) as AnsweredCOOMEVA,
		SUM(AbandonedCOOMEVA) as AbandonedCOOMEVA,
		SUM(TransferidasCOOMEVA) as TransferidasCOOMEVA,
		SUM(TransferidasMinCOOMEVA) as TransferidasMinCOOMEVA,
		SUM(SLA20CallsCOOMEVA)/SUM(VolumenCOOMEVA) as SLA20COOMEVA,
		SUM(SLA30CallsCOOMEVA)/SUM(VolumenCOOMEVA) as SLA30COOMEVA,
		SUM(AbandonedCOOMEVA)/SUM(VolumenCOOMEVA) as AbandonCOOMEVA,
		SUM(TalkingTimeCOOMEVA)/SUM(AnsweredCOOMEVA) as AHTCOOMEVA,
		SUM(WaitingTimeCOOMEVA)/SUM(AnsweredCOOMEVA) as ASACOOMEVA,
		SUM(TalkingTimeCOOMEVA) as TalkingTimeCOOMEVA,
        SUM(VolumenVECI) as VolumenVECI,
		SUM(AnsweredVECI) as AnsweredVECI,
		SUM(AbandonedVECI) as AbandonedVECI,
		SUM(TransferidasVECI) as TransferidasVECI,
		SUM(TransferidasMinVECI) as TransferidasMinVECI,
		SUM(SLA20CallsVECI)/SUM(VolumenVECI) as SLA20VECI,
		SUM(SLA30CallsVECI)/SUM(VolumenVECI) as SLA30VECI,
		SUM(AbandonedVECI)/SUM(VolumenVECI) as AbandonVECI,
		SUM(TalkingTimeVECI)/SUM(AnsweredVECI) as AHTVECI,
		SUM(WaitingTimeVECI)/SUM(AnsweredVECI) as ASAVECI,
		SUM(TalkingTimeVECI) as TalkingTimeVECI,
        SUM(VolumenLiverpool) as VolumenLiverpool,
		SUM(AnsweredLiverpool) as AnsweredLiverpool,
		SUM(AbandonedLiverpool) as AbandonedLiverpool,
		SUM(TransferidasLiverpool) as TransferidasLiverpool,
		SUM(TransferidasMinLiverpool) as TransferidasMinLiverpool,
		SUM(SLA20CallsLiverpool)/SUM(VolumenLiverpool) as SLA20Liverpool,
		SUM(SLA30CallsLiverpool)/SUM(VolumenLiverpool) as SLA30Liverpool,
		SUM(AbandonedLiverpool)/SUM(VolumenLiverpool) as AbandonLiverpool,
		SUM(TalkingTimeLiverpool)/SUM(AnsweredLiverpool) as AHTLiverpool,
		SUM(WaitingTimeLiverpool)/SUM(AnsweredLiverpool) as ASALiverpool,
		SUM(TalkingTimeLiverpool) as TalkingTimeLiverpool,
		SUM(LocalizadoresTotal)/(SUM(AnsweredTotal)-SUM(TransferidasMinTotal)) as FCTotal,
		SUM(LocalizadoresTotal) as LocalizadoresTotal,
		SUM(MontoTotal)*Dolar as MontoTotal,
		SUM(LocalizadoresMP)/(SUM(AnsweredMP)-SUM(TransferidasMinMP)) as FCMP,
		SUM(LocalizadoresMP) as LocalizadoresMP,
		SUM(MontoMP)*Dolar as MontoMP,
		SUM(LocalizadoresIT)/(SUM(AnsweredIT)-SUM(TransferidasMinIT)) as FCIT,
		SUM(LocalizadoresIT) as LocalizadoresIT,
		SUM(MontoIT)*Dolar as MontoIT,
		SUM(LocalizadoresCOPA)/(SUM(AnsweredCOPA)-SUM(TransferidasMinCOPA)) as FCCOPA,
		SUM(LocalizadoresCOPA) as LocalizadoresCOPA,
		SUM(MontoCOPA)*Dolar as MontoCOPA,
		SUM(LocalizadoresCOOMEVA)/(SUM(AnsweredCOOMEVA)-SUM(TransferidasMinCOOMEVA)) as FCCOOMEVA,
		SUM(LocalizadoresCOOMEVA) as LocalizadoresCOOMEVA,
		SUM(MontoCOOMEVA)*Dolar as MontoCOOMEVA,
		SUM(LocalizadoresVECI)/(SUM(AnsweredVECI)-SUM(TransferidasMinVECI)) as FCVECI,
		SUM(LocalizadoresVECI) as LocalizadoresVECI,
		SUM(MontoVECI)*Dolar as MontoVECI,
		SUM(LocalizadoresLiverpool)/(SUM(AnsweredLiverpool)-SUM(TransferidasMinLiverpool)) as FCLiverpool,
		SUM(LocalizadoresLiverpool) as LocalizadoresLiverpool,
		SUM(MontoLiverpool)*Dolar as MontoLiverpool,
		1-SUM(PNP)/SUM(DuracionSesiones) as UtilizacionTotal,
		(SUM(TalkingTimeTotal)+SUM(PP))/(SUM(DuracionSesiones)-SUM(PNP)) as OcupacionTotal,
		AVG(AsesoresPrimerNivel) as AsesoresPrimerNivelTotal,
		AVG(AsesoresApoyo) as AsesoresApoyoTotal,
		Dolar as TipoDeCambio
	FROM
		(
			SELECT
				Fecha, Dolar
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) D_Fechas
	LEFT JOIN
		(
			SELECT
				a.Fecha as Telefonia_Fecha, Skill,
				COUNT(ac_id) as VolumenTotal,
				COUNT(IF(Answered=1,ac_id,NULL)) as AnsweredTotal,
				COUNT(IF(Answered=0,ac_id,NULL)) as AbandonedTotal,
				COUNT(IF(Desconexion='Transferida',ac_id,NULL)) as TransferidasTotal,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30',ac_id,NULL)) as TransferidasMinTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) as SLA20CallsTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:30',ac_id,NULL)) as SLA30CallsTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Espera),0)) as WaitingTimeTotal,
				COUNT(IF(Canal LIKE '%MP MX%',ac_id,NULL)) as VolumenMP,
				COUNT(IF(Answered=1 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AnsweredMP,
				COUNT(IF(Answered=0 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AbandonedMP,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMP,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMinMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA20CallsMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA30CallsMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Espera),0)) as WaitingTimeMP,
				COUNT(IF(Canal LIKE '%Intertours%',ac_id,NULL)) as VolumenIT,
				COUNT(IF(Answered=1 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AnsweredIT,
				COUNT(IF(Answered=0 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AbandonedIT,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasIT,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasMinIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA20CallsIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA30CallsIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Espera),0)) as WaitingTimeIT,
				COUNT(IF(Canal LIKE '%copa%',ac_id,NULL)) as VolumenCOPA,
				COUNT(IF(Answered=1 AND Canal LIKE '%copa%',ac_id,NULL)) as AnsweredCOPA,
				COUNT(IF(Answered=0 AND Canal LIKE '%copa%',ac_id,NULL)) as AbandonedCOPA,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasCOPA,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasMinCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA20CallsCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA30CallsCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Espera),0)) as WaitingTimeCOPA,
				COUNT(IF(Canal LIKE '%coomeva%',ac_id,NULL)) as VolumenCOOMEVA,
				COUNT(IF(Answered=1 AND Canal LIKE '%coomeva%',ac_id,NULL)) as AnsweredCOOMEVA,
				COUNT(IF(Answered=0 AND Canal LIKE '%coomeva%',ac_id,NULL)) as AbandonedCOOMEVA,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%coomeva%',ac_id,NULL)) as TransferidasCOOMEVA,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%coomeva%',ac_id,NULL)) as TransferidasMinCOOMEVA,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%coomeva%',ac_id,NULL)) as SLA20CallsCOOMEVA,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%coomeva%',ac_id,NULL)) as SLA30CallsCOOMEVA,
				SUM(IF(Answered=1 AND Canal LIKE '%coomeva%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeCOOMEVA,
				SUM(IF(Answered=1 AND Canal LIKE '%coomeva%', TIME_TO_SEC(Espera),0)) as WaitingTimeCOOMEVA,
                COUNT(IF(Canal LIKE '%veci%',ac_id,NULL)) as VolumenVECI,
				COUNT(IF(Answered=1 AND Canal LIKE '%veci%',ac_id,NULL)) as AnsweredVECI,
				COUNT(IF(Answered=0 AND Canal LIKE '%veci%',ac_id,NULL)) as AbandonedVECI,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%veci%',ac_id,NULL)) as TransferidasVECI,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%veci%',ac_id,NULL)) as TransferidasMinVECI,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%veci%',ac_id,NULL)) as SLA20CallsVECI,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%veci%',ac_id,NULL)) as SLA30CallsVECI,
				SUM(IF(Answered=1 AND Canal LIKE '%veci%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeVECI,
				SUM(IF(Answered=1 AND Canal LIKE '%veci%', TIME_TO_SEC(Espera),0)) as WaitingTimeVECI,
                COUNT(IF(Canal LIKE '%liverpool%',ac_id,NULL)) as VolumenLiverpool,
				COUNT(IF(Answered=1 AND Canal LIKE '%liverpool%',ac_id,NULL)) as AnsweredLiverpool,
				COUNT(IF(Answered=0 AND Canal LIKE '%liverpool%',ac_id,NULL)) as AbandonedLiverpool,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%liverpool%',ac_id,NULL)) as TransferidasLiverpool,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:30' AND Canal LIKE '%liverpool%',ac_id,NULL)) as TransferidasMinLiverpool,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%liverpool%',ac_id,NULL)) as SLA20CallsLiverpool,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%liverpool%',ac_id,NULL)) as SLA30CallsLiverpool,
				SUM(IF(Answered=1 AND Canal LIKE '%liverpool%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeLiverpool,
				SUM(IF(Answered=1 AND Canal LIKE '%liverpool%', TIME_TO_SEC(Espera),0)) as WaitingTimeLiverpool,
				COUNT(DISTINCT IF(`id Departamento`=$dept,asesor,NULL)) as AsesoresPrimerNivel,
				COUNT(DISTINCT IF(`id Departamento`!=$dept or `id Departamento` IS NULL,asesor,NULL)) as AsesoresApoyo
			FROM
				t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			LEFT JOIN
				Asesores c
			ON
				a.asesor=c.id
			WHERE
				Skill=$dept AND
				a.Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				a.Fecha
		) Telefonia
	ON
		Fecha=Telefonia_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Localizadores_Fecha,
				COUNT(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresTotal,
				SUM(IF(`id Departamento`=$dept   OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoTotal,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresMP,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoMP,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%intertours%'),localizador,NULL)) as LocalizadoresIT,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%intertours%'),Monto,NULL)) as MontoIT,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%copa%'),localizador,NULL)) as LocalizadoresCOPA,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%copa%'),Monto,NULL)) as MontoCOPA,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%coomeva%'),localizador,NULL)) as LocalizadoresCOOMEVA,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%coomeva%'),Monto,NULL)) as MontoCOOMEVA,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%corteingles%'),localizador,NULL)) as LocalizadoresVECI,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%corteingles%'),Monto,NULL)) as MontoVECI,
				COUNT(IF((`id Departamento`=$dept   AND Afiliado LIKE '%liverpool%'),localizador,NULL)) as LocalizadoresLiverpool,
				SUM(IF((`id Departamento`=$dept   AND Afiliado LIKE '%liverpool%'),Monto,NULL)) as MontoLiverpool

			FROM
				(
					SELECT
						Fecha, Localizador, asesor, `id Departamento`, Afiliado, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta
					FROM
							t_Locs a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						a.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						a.Fecha, localizador
					HAVING
						Monto!=0 AND
						Venta!=0
				) Locs
			GROUP BY
				Fecha
		) Localizadores
	ON
		Fecha=Localizadores_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Pausas_Fecha,
				SUM(IF(Skill=$dept AND Productiva=0,TIME_TO_SEC(Duracion),0)) as PNP,
				SUM(IF(Skill=$dept AND Productiva=1,TIME_TO_SEC(Duracion),0)) as PP
			FROM
				t_pausas a
			LEFT JOIN
				Tipos_pausas b
			ON
				a.codigo=b.pausa_id
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) Pausas
	ON
		Fecha=Pausas_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha,
				SUM(IF(Skill=$dept,TIME_TO_SEC(Duracion),0)) as DuracionSesiones
			FROM
				t_Sesiones
			WHERE
				Fecha_in BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha_in
		) Sesiones
	ON
		Fecha=Sesiones_Fecha
	";



$result=mysql_query($query);

$num=mysql_numrows($result);

$numfield=mysql_num_fields($result);

$i=0;

while($i<$numfield){

    $field[$i]=mysql_field_name($result,$i);

$i++;

}

$i=0;

while($i<$num){

    $x=0;

     while($x<$numfield){

        $data[$field[$x]][$i]=mysql_result($result,$i,$field[$x]);

     $x++;

     }

     $TotalFechas=$i;

$i++;

}

$result_ac=mysql_query($query_ac);

$numfield_ac=mysql_num_fields($result_ac);

$i=0;

while($i<$numfield_ac){

    $field_ac[$i]=mysql_field_name($result_ac,$i);

$i++;

}

$x=0;

while($x<$numfield_ac){

    $data_ac[$field_ac[$x]]=mysql_result($result_ac,0,$field_ac[$x]);

$x++;

}


/*
//Locs Salinillas
if($dept==3){

$query="SELECT Fecha as Fecha_sal, COUNT(DISTINCT Localizador)	as Locs_sal, SUM(Monto) as Monto_sal
	FROM
		(
			SELECT
				b.Fecha,
				Localizador,
				ammount*Dolar as Monto
			FROM
				(
					SELECT Fecha, Localizador, (Venta+OtrosIngresos+Egresos) as ammount
					FROM t_Locs
					WHERE
						Venta!=0 AND
						Afiliado LIKE '%agentes.pricetravel.com.mx%' AND
						asesor=0
					HAVING
						ammount!=0
				) a
			RIGHT JOIN
				Fechas b
			ON
				a.Fecha=b.Fecha
			WHERE
				b.Fecha>='$from' AND
				b.Fecha<='$to'
		) Locs
	GROUP BY
		Fecha
    ORDER BY
        Fecha";



$result=mysql_query($query);
$num=mysql_numrows($result);
$numfieldsal=mysql_num_fields($result);
$i=0;
while($i<$numfieldsal){
    $fieldsal[$i]=mysql_field_name($result,$i);
$i++;
}

$i=0;

while($i<$num){
    $x=0;
    while($x<$numfieldsal){
        $data[$fieldsal[$x]][$i]=mysql_result($result,$i,$fieldsal[$x]);
        //echo "$fieldsal[$x]: ".mysql_result($result,$i,$fieldsal[$x])."<br>";
     $x++;
     }

$i++;
}

//Query Acumulado Salinillas
$query="SELECT Fecha as Fecha_sal, COUNT(DISTINCT Localizador)	as Locs_sal, SUM(Monto) as Monto_sal
	FROM
		(
			SELECT
				b.Fecha,
				Localizador,
				ammount*Dolar as Monto
			FROM
				(
					SELECT Fecha, Localizador, (Venta+OtrosIngresos+Egresos) as ammount
					FROM t_Locs
					WHERE
						Venta!=0 AND
						Afiliado LIKE '%agentes.pricetravel.com.mx%' AND
						asesor=0
					HAVING
						ammount!=0
				) a
			RIGHT JOIN
				Fechas b
			ON
				a.Fecha=b.Fecha
			WHERE
				b.Fecha>='$from' AND
				b.Fecha<='$to'
		) Locs
	";



$result=mysql_query($query);
$num=mysql_numrows($result);
$numfieldsalac=mysql_num_fields($result);
$i=0;
while($i<$numfieldsalac){
    $fieldsalac[$i]=mysql_field_name($result,$i);
$i++;
}

$i=0;

    $x=0;
    while($x<$numfieldsalac){
        $data_ac[$fieldsalac[$x]]=mysql_result($result,$i,$fieldsalac[$x]);
        if($fieldsalac[$x]=='Locs_sal'){
            $data_ac['LocalizadoresTotal']=intval($data_ac['LocalizadoresTotal'])+intval($data_ac[$fieldsalac[$x]]);
            $data_ac['LocalizadoresMP']=$data_ac['LocalizadoresMP']+$data_ac[$fieldsalac[$x]];
            //echo "$fieldsalac[$x]: ".mysql_result($result,$i,$fieldsalac[$x])."<br>";
        }
        if($fieldsalac[$x]=='Monto_sal'){
            $data_ac['MontoTotal']=$data_ac['MontoTotal']+$data_ac[$fieldsalac[$x]];
            $data_ac['MontoMP']=$data_ac['MontoMP']+$data_ac[$fieldsalac[$x]];
        }
     $x++;
     }


//print_r($data_ac);
}
*/
?>



<div id="accordion">

  <h3>Informacion por dia</h3>

    <div>

    <table width='100%' class='tablesorter' id='tablesorter' style='text-align:center'>

    <thead>





<?php

    printRows('Fecha','M&eacutetrica','Canal','na','th');

?>

    </thead>

    <tbody>

<?php

printRows('AbandonCOOMEVA','Abandon%','COOMEVA','%');
printRows('AbandonedCOOMEVA','Abandonadas','COOMEVA','num');
printRows('AHTCOOMEVA','AHT','COOMEVA','num');
printRows('AnsweredCOOMEVA','Contestadas','COOMEVA','num');
printRows('ASACOOMEVA','ASA','COOMEVA','num');
printRows('FCCOOMEVA','FC%','COOMEVA','%');
printRows('LocalizadoresCOOMEVA','Localizadores','COOMEVA','num');
printRows('MontoCOOMEVA','Monto$','COOMEVA','$');
printRows('SLA20COOMEVA','SLA20seg','COOMEVA','%');
printRows('SLA30COOMEVA','SLA30seg','COOMEVA','%');
printRows('TalkingTimeCOOMEVA','TalkingTime','COOMEVA','num');
printRows('TransferidasCOOMEVA','Transferidas','COOMEVA','num');
printRows('TransferidasMinCOOMEVA','Transferidas<1min','COOMEVA','num');
printRows('VolumenCOOMEVA','Volumen','COOMEVA','num');

printRows('AbandonLiverpool','Abandon%','Liverpool','%');
printRows('AbandonedLiverpool','Abandonadas','Liverpool','num');
printRows('AHTLiverpool','AHT','Liverpool','num');
printRows('AnsweredLiverpool','Contestadas','Liverpool','num');
printRows('ASALiverpool','ASA','Liverpool','num');
printRows('FCLiverpool','FC%','Liverpool','%');
printRows('LocalizadoresLiverpool','Localizadores','Liverpool','num');
printRows('MontoLiverpool','Monto$','Liverpool','$');
printRows('SLA20Liverpool','SLA20seg','Liverpool','%');
printRows('SLA30Liverpool','SLA30seg','Liverpool','%');
printRows('TalkingTimeLiverpool','TalkingTime','Liverpool','num');
printRows('TransferidasLiverpool','Transferidas','Liverpool','num');
printRows('TransferidasMinLiverpool','Transferidas<1min','Liverpool','num');
printRows('VolumenLiverpool','Volumen','Liverpool','num');

printRows('AbandonVECI','Abandon%','VECI','%');
printRows('AbandonedVECI','Abandonadas','VECI','num');
printRows('AHTVECI','AHT','VECI','num');
printRows('AnsweredVECI','Contestadas','VECI','num');
printRows('ASAVECI','ASA','VECI','num');
printRows('FCVECI','FC%','VECI','%');
printRows('LocalizadoresVECI','Localizadores','VECI','num');
printRows('MontoVECI','Monto$','VECI','$');
printRows('SLA20VECI','SLA20seg','VECI','%');
printRows('SLA30VECI','SLA30seg','VECI','%');
printRows('TalkingTimeVECI','TalkingTime','VECI','num');
printRows('TransferidasVECI','Transferidas','VECI','num');
printRows('TransferidasMinVECI','Transferidas<1min','VECI','num');
printRows('VolumenVECI','Volumen','VECI','num');

printRows('AbandonCOPA','Abandon%','COPA','%');
printRows('AbandonedCOPA','Abandonadas','COPA','num');
printRows('AHTCOPA','AHT','COPA','num');
printRows('AnsweredCOPA','Contestadas','COPA','num');
printRows('ASACOPA','ASA','COPA','num');
printRows('FCCOPA','FC%','COPA','%');
printRows('LocalizadoresCOPA','Localizadores','COPA','num');
printRows('MontoCOPA','Monto$','COPA','$');
printRows('SLA20COPA','SLA20seg','COPA','%');
printRows('SLA30COPA','SLA30seg','COPA','%');
printRows('TalkingTimeCOPA','TalkingTime','COPA','num');
printRows('TransferidasCOPA','Transferidas','COPA','num');
printRows('TransferidasMinCOPA','Transferidas<1min','COPA','num');
printRows('VolumenCOPA','Volumen','COPA','num');

printRows('AbandonedIT','Abandonadas','IT','num');
printRows('AbandonIT','Abandon%','IT','%');
printRows('AHTIT','AHT','IT','num');
printRows('AnsweredIT','Contestadas','IT','num');
printRows('ASAIT','ASA','IT','num');
printRows('FCIT','FC%','IT','%');
printRows('LocalizadoresIT','Localizadores','IT','num');
printRows('MontoIT','Monto$','IT','$');
printRows('SLA20IT','SLA20seg','IT','%');
printRows('SLA30IT','SLA30seg','IT','%');
printRows('TalkingTimeIT','TalkingTime','IT','num');
printRows('TransferidasIT','Transferidas','IT','num');
printRows('TransferidasMinIT','Transferidas<1min','IT','num');
printRows('VolumenIT','Volumen','IT','num');

printRows('AbandonedMP','Abandonadas','MP','num');
printRows('AbandonMP','Abandon%','MP','%');
printRows('AHTMP','AHT','MP','num');
printRows('AnsweredMP','Contestadas','MP','num');
printRows('ASAMP','ASA','MP','num');
printRows('FCMP','FC%','MP','%');
printRows('LocalizadoresMP','Localizadores','MP','num');
printRows('MontoMP','Monto$','MP','$');
printRows('SLA20MP','SLA20seg','MP','%');
printRows('SLA30MP','SLA30seg','MP','%');
printRows('TalkingTimeMP','TalkingTime','MP','num');
printRows('TransferidasMinMP','Transferidas<1min','MP','num');
printRows('TransferidasMP','Transferidas','MP','num');
printRows('VolumenMP','Volumen','MP','num');

printRows('AbandonedTotal','Abandonadas','Total','num');
printRows('AbandonTotal','Abandon%','Total','%');
printRows('AHTTotal','AHT','Total','num');
printRows('AnsweredTotal','Contestadas','Total','num');
printRows('ASATotal','ASA','Total','num');
printRows('AsesoresApoyoTotal','Asesoresconectados<br>Apoyo','Total','na');
printRows('AsesoresPrimerNivelTotal','Asesoresconectados<br>FirstLevel','Total','na');
printRows('FCTotal','FC%','Total','%');
printRows('LocalizadoresTotal','Localizadores','Total','num');
printRows('MontoTotal','Monto$','Total','$');
printRows('OcupacionTotal','Ocupaci&oacuten','Total','%');
printRows('SLA20Total','SLA20seg','Total','%');
printRows('SLA30Total','SLA30seg','Total','%');
printRows('TalkingTimeTotal','TalkingTime','Total','num');
printRows('TipoDeCambio','TipodeCambio','Total','$');
printRows('TransferidasMinTotal','Transferidas<1min','Total','num');
printRows('TransferidasTotal','Transferidas','Total','num');
printRows('UtilizacionTotal','Utilizaci&oacuten','Total','%');
printRows('VolumenTotal','Volumen','Total','num');

?>

            </tbody>

            </table>

        </div>

    <h3>Acumulado de fechas Seleccionadas</h3>

        <div>

            <table width='100%' id='acumulado' style='text-align: center'>

                <thead>

                <tr class='title' style='text-align: left'>

                    <th>Concepto</th>

                    <th>Total</th>

                    <th>MP</th>

                    <th>IT</th>

                    <th>COPA</th>

                    <th>COOMEVA</th>

                    <th>Liverpool</th>

                    <th>VECI</th>

                </tr>

                </thead>

                <tbody>

                <?php

                  printRows_ac('Volumen','Volumen','Total','num');
                  printRows_ac('Answered','Contestadas','Total','num');
                  printRows_ac('Abandoned','Abandonadas','Total','num');
                  printRows_ac('Transferidas','Transferidas','Total','num');
                  printRows_ac('TransferidasMin','Transferidas <1 min','Total','num');
                  printRows_ac('SLA20','SLA 20 seg','Total','%');
                  printRows_ac('SLA30','SLA 30 seg','Total','%');
                  printRows_ac('Abandon','Abandon %','Total','%');
                  printRows_ac('AHT','AHT','Total','num');
                  printRows_ac('ASA','ASA','Total','num');
                  printRows_ac('TalkingTime','Talking Time','Total','num');
                  printRows_ac('Localizadores','Localizadores','Total','num');
                  printRows_ac('FC','FC %','Total','%');
                  printRows_ac('Monto','Monto $','Total','$');
                  printRows_ac('Utilizacion','Utilizaci&oacuten','Total','%');
                  printRows_ac('Ocupacion','Ocupaci&oacuten','Total','%');
                  printRows_ac('AsesoresPrimerNivel','Asesores conectados<br>First Level','Total','na');
                   printRows_ac('AsesoresApoyo','Asesores conectados<br>Apoyo','Total','na');




                ?>

            </tbody>

            </table>



        </div>



</div>



