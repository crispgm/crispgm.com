---
layout: post
type: lifestyle
title: Accounting with Plain Text Accounting - The Fundamental
permalink: /page/accounting-with-pta-fundamental.html
---

## Why Accounting

Accounting has been my good habit since university life. With the help of accounting, I could track my money, including incomes, outcomes and investment. And I later start to not only track money, but also make plans for annual budgets and set boundaries for each kind of consumption.

And I also think it is enjoyable.

But I am not satisfied with the method or methodology of my accounting process.

I firstly used Evernote for simple tabular accounting. And later I transfered the accounting books to Microsoft OneNote, which were still with tables. Microsoft OneNote is not designed for bookkeping. The way it shows data is not bad, however, it lacks functionalities like calculations, currencies, and most important—statistics.

Though I have hept accounting book from 2014 to now, which means six years books, I could not be able to figure out what do exactly I spend money on every category.

## Tool Finding

Microsoft OneNote is not the right way. And I had been on my way find the right tool for accounting since about 3 years ago.

I firstly tried accounting softwares and services. The major problem of those GUI-centric softwares is that they are over complicated, at least with GUI. I tried MoneyWiz and 随手记. They provide tons of features, e.g. [MoneyWiz provides over 400 features](https://wiz.money/features/), and I don’t like that.

The mobile-first applications are over simple and with no potentials. We can always log, view charts, but nothing more. For simple use, it may be fairly okay. But the mobile platform restrictions block them from being efficient only if we sacrifice our data to automation system (while desktop applications also provide). e.g. credit bill passing. This causes privacy issues and concerns.

I was considering use Microsoft Excel or equivalence. It provides a much more powerful table so that I can figure out calculations and statistics. And the books should be stored with Dropbox. It sounds okay but it’s still tables.  Moreover, I am actually not an Office person and I do not want to pay for the expensive Office software.

I tried Quip for a while and it is good for project management. But its embedded spreadsheet is similar to Microsoft OneNote. Other note taking, project collaborating tools, or lightweight Office replacements have the same problem. So Quip, Google Docs, Bear, and even Notion, all of them are not the right choice.

I kept on accounting with Microsoft OneNote until I rediscovered Plain Text Accounting.

## Plain Text Accounting

I first heared about Plain Text Accounting in a [blog post](https://www.byvoid.com/zht/blog/beancount-bookkeeping-1) from BYVoid in the middle of 2019, which talks about double entry bookkeeping with Beancount. I read the post and the idea is attractive.

I was curious about the idea but the practice mentioned in the post is neither clear nor educational enough. But BYVoid also recomends Ledger and HLedger, that’s where I start to dive into PTA.

## The Benefits

### Double-entry bookkeeping

Ledger-likes are keeping accounting with double-entry bookkeeping.

> For every movement of value (a transaction), both the source and destination are recorded. Simple arithmetic invariants help prevent errors. [^1]

Double-entry bookkeeping could date back to early-medieval Middle East and it is widely used in modern accounting.

For me, the single-entry books only contains expenses and I am just not able to know which payment method I used at that time and which method are my preferred choice. It is a simpler way but not the best way.

### Simplicity & Minimalism

Plain Text Accounting is also a minimalist choice. You can log the transaction with simple human readable texts almost everywhere.

```
2020/01/01 Arabica coffee
    expenses:drinks:coffee     75
    liabilities:credit:bankcomm
```

### Extensible

The ledger’s file format is fairly open. There are a variety of open source libraries to interpret the file. This enables unlimited ideas to be implemented at your own taste.

And ledger file is in structured data format. Therefore, it is easy to sort and calculate, so that statistical work can be done easily.

Certainly, common usages may have already been implemented by contributors and we can find tons of extensions.

### Versioning

Accounting data is valuable so that we need to know the changes and revision-control it.

Plain Text Accounting is based on text files so it is easy to do versioning. And Git, the commonly used version control system, definitely works perfect with it.

### Own Your Data

Another major concern about accounting is data security and privacy. Accounting books are highly private and require data integrity.

The ledger software provides only the computing and processing part. We can choose a trustworthy storage and encrypt the journals. That’s why we own our data.

## My Practice

### Hledger

BYVoid actually uses [Beancount](http://furius.ca/beancount/)  for PTA. After I inspected the project, I basically think it is mediocre and the docs are not good. Another two recommendations from BYVoid are Ledger and Hledger.

Ledger is the origin of plain text accounting, invented by John Wiegley in 2003. It is written in C++ language. Hledger and Beancount are both created with the inspiration of Ledger. Ledger is like a hardcore hacker tool with only command line interface. The docs are GNU manual like, which is pretty hard to read.

I think Hledger is the best one. The reason is simple:
- Hledger is more user friendly, integrates with CLI, TUI, and simple web UI.
- Hledger is actively under development.
- Hledger is based on the idea of Ledger, they are compatible in a way.
- Hledger has greater docs, though it is really a little bit messy but it is handy.

---

And there might be another chapter to show my practice on using plain text accounting.

### References

[^1]: What is double-entry? https://plaintextaccounting.org/
