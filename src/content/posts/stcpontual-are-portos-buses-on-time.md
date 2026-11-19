---
title: "STCPontual — are Porto's buses on time?"
description: "My hometown, Porto, has been pushing to get people onto public transport. In July, city hall made it free for residents, the tram lines are being extended,…"
date: "2026-09-20T15:27:36"
updated: "2026-09-20T15:46:06"
tags: ["Data Analysis", "Data Visualization"]
pinned: true
readingTime: 1
draft: true
---

My hometown, Porto, has been pushing to get people onto public transport. [In July, city hall made it free for residents](https://portaldomunicipe.cm-porto.pt/pt/-/transportes-p%C3%BAblicos-gratuitos), the tram lines are being extended, parking downtown is being made harder, among other policies. However, in my opinion, price was never the thing holding people back. It's quality of service, specifically how unreliable buses are at arriving on time.

Around the same time, [in June, STCP integrated with Google Maps](https://stcp.pt/pt/stcp-chega-em-tempo-real-ao-google-maps) to show the live positions of its fleet and estimated arrival times at stops. That data is public, and so is the timetable it's supposed to be keeping to. Nobody was putting the two side by side.

So I built [STCPontual](https://github.com/goncalovf/stcpontual). It polls the live vehicle positions, imports STCP's schedule, matches one against the other, and publishes the result: how puntual are STCP's buses? What are the worst hours of the day, worst days of the week, least reliable lines, and whether any of it is getting better.

<div class="buttons"><a class="button" href="https://stcpontual.pt/">Go to STCPontual</a></div>
