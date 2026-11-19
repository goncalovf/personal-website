---
title: "ERD of an investment robo-advisor app"
description: "Partial ERD of an investment robo-advisor app like Wealthfront or Betterment"
date: "2021-05-04T16:22:00"
updated: "2021-10-04T16:26:28"
tags: ["Database Design"]
pinned: false
readingTime: 1
---

I'm a fan of learning-by-doing, so to learn about database design, I built an ERD for a hypothetical investment robo-advising app, like [Betterment](https://www.betterment.com/)'s or [Wealthfront](https://www.wealthfront.com/)'s.

<figure><a href="/wp-content/uploads/2021/10/ERD-roboadvisor-goncalo-figueiredo.jpg"><img loading="lazy" width="3942" height="1971" src="/wp-content/uploads/2021/10/ERD-roboadvisor-goncalo-figueiredo.jpg" alt=""></a></figure>

(click on the image to enlarge it)

Of course, a robo-advising app should be incredibly complex, so this ERD should only cover a small part of its functionality. But in my opinion, it was already a good exercise to think about some design choices. For example:

- How to logically structure an app into distinct components, which can help to better organize development work.
- Which attributes should be kept simple (existing explicitly) or derived (calculated through other attributes), and how to make this decision taking into account the importance of performance and of reducing the risk of anomalies.
- What's the right balance between normalization and performance.
- How to design tables for logging/auditing.
- The difference between 1:1, 1:N, and N:N relationships, and how to represent them.
- The difference between entities and weak entities.

I built this ERD with [draw.io](https://draw.io/). If you want to extend it, download the [source code](https://github.com/goncalovf/erd-robo-advisor) and import it to [draw.io](https://draw.io/).
