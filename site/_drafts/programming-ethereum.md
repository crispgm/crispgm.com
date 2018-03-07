---
layout: post
title: 区块链浅入：我的第一个以太坊程序 Cryptoflags
permalink: /page/programming-ethereum.html
type: programming
---

最近区块链技术大火，到处都在谈论和蹭热点，以至于豆瓣留言上看到了区块链的言论。于是，当事有些愤怒：

> “不要什么都扯到区块链，现在火到这样子又几乎没几个人真明白怎么回事，简直是个笑话。”

即使是作为“资深”程序员的我，起初也对于比特币和区块链云里雾里的感觉，原理和应用处于模糊的状态，只知道有各种币的交易所可以“炒币”。但春节前夕，因为参加了 [Beijing Bitcoin Cash Meetup](https://www.meetup.com/Beijing-Bitcoin-Cash-Meetup/)，之后持续关注加密货币交易、区块链技术和应用，以及免不了的奇闻逸事。

逐渐对区块链技术有了一些了解，在此推荐[深入浅出区块链](https://learnblockchain.cn/)。终于，我决定开发第一个智能合约——Cryptoflags。

本文会从前到后把一个分布式应用（Dapp）调通，理论部分不会过多涉及。

# Ethereum

Ethereum，中文译为以太坊，它是：

> 一个建立在区块链技术之上，去中心化应用平台。它允许任何人在平台中建立和使用通过区块链技术运行的去中心化应用。

比特币是创造了区块链，但比特币并没有独立抽象出区块链。在 Ethereum 出现之前，需要 Copy 或根据比特币的代码，修改比特币底层的加密算法、共识机制等，然后在此之上开发实际的应用部分，开发成本相当高。

而 Ethereum 对底层区块链技术进行了封装，让开发者可以直接基于 Ethereum 开发区块链应用。开发者只需要专注于应用本身的开发，而不需要重复实现区块链底层，大大降低了开发成本。

## Ethereum 开发

在 Ethereum 上开发程序称为智能合约（Smart Contract）。比特币的交易也是可以编程的，使用的是比特币脚本，但基于比特币的一些设计原因，比特币脚本不支持循环。而相比比特币脚本的受限，Ethereum 是图灵完备的。

在 Ethereum 开发中，最常见的语言是 [Solidity](https://github.com/ethereum/solidity)。据说 Solidity 和 JavaScript 很相似，但其实我觉得并不是。

## 准备工作

### [Remix](https://remix.ethereum.org/)

Ethereum 官方的 Solidity 在线 IDE，用于调试合约部分非常好用。体积有些，国内网络打开可能会十分的慢。请耐心等待。

### [Truffle](http://truffleframework.com/)

Truffle 是一个 Node.js 编写的合约开发框架。

### [Ganache](http://truffleframework.com/ganache/)

也是 Truffle 出品的一个带 GUI 的 Ethereum 调试客户端，可以在本地创建一个 Ethereum 网络并且提供 JSON RPC 调用。

### [Web3.js](https://github.com/ethereum/web3.js/)

可以认为是 Ethereum 的 JS SDK，封装了它的 JSON APi。在这里会用 Node.js 作为应用的传统后端存在，通过 Web3.js 与 Ethereum 交互。

## 合约

## 后端

## 前端

# 结论
