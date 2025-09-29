import React, { useState } from "react";
import { motion } from "framer-motion";

// Odeka Media — Homepage inspired by openai.com layout principles
// Tech: React + TailwindCSS (no external UI libs required)
// NOTE: Plain React (no TS types). Fixed JSX root issues by wrapping adjacent elements.

export default function OdekaHome() {
  const [tab, setTab] = useState("Odeka"); // "Odeka" | "Media"
  const [locale, setLocale] = useState("en-US");
  const [currency, setCurrency] = useState("XOF");

  return (
    <div className="min-h-screen bg-neutral-950 text-neutral-100 selection:bg-neutral-800 selection:text-white">
      <Header tab={tab} onChangeTab={setTab} />
      <Hero />
      <Access />
      <TabSwitcher tab={tab} onChangeTab={setTab} />
      {tab === "Odeka" ? (
        <OdekaTab locale={locale} currency={currency} />
      ) : (
        <MediaTab />
      )}
      <Footer
        locale={locale}
        currency={currency}
        onChangeLocale={setLocale}
        onChangeCurrency={setCurrency}
      />
    </div>
  );
}

function Header({ tab, onChangeTab }) {
  return (
    <div className="sticky top-0 z-40 backdrop-blur border-b border-neutral-900/60">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="flex h-16 items-center justify-between">
          {/* Brand */}
          <a href="#top" className="flex items-center gap-3">
            <div className="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0 shadow-[0_0_30px_-10px_rgba(255,255,255,0.7)]" />
            <span className="font-semibold tracking-tight">Odeka Media</span>
          </a>

          {/* Tabs in header */}
          <div className="hidden md:flex items-center gap-2 p-1 rounded-full border border-neutral-800">
            {["Odeka", "Media"].map((label) => (
              <button
                key={label}
                onClick={() => onChangeTab(label)}
                aria-current={tab === label}
                className={
                  "px-4 py-1.5 text-sm rounded-full transition " +
                  (tab === label ? "bg-white text-neutral-900" : "text-neutral-300 hover:text-white")
                }
              >
                {label}
              </button>
            ))}
          </div>

          {/* Actions */}
          <div className="flex items-center gap-3">
            <a
              href="#signin"
              className="hidden sm:inline-flex rounded-full border border-neutral-800 px-4 py-2 text-sm hover:border-neutral-700"
            >
              Sign in
            </a>
            <a
              href="#open-app"
              className="inline-flex rounded-full bg-white text-neutral-900 px-4 py-2 text-sm font-medium hover:bg-neutral-200"
            >
              Open the app
            </a>
          </div>
        </div>
      </div>
    </div>
  );
}

function Hero() {
  return (
    <section className="relative overflow-hidden">
      {/* FIX: Wrap background + content in a single parent to avoid adjacent JSX elements */}
      <div className="relative">
        <div className="pointer-events-none absolute inset-0 -z-10">
          <div className="absolute left-1/2 top-[-10%] h-[700px] w-[900px] -translate-x-1/2 rounded-full bg-[radial-gradient(closest-side,rgba(255,255,255,0.12),transparent_70%)] blur-2xl" />
          <div className="absolute right-[-10%] bottom-[-20%] h-[500px] w-[500px] rounded-full bg-[conic-gradient(from_180deg_at_50%_50%,rgba(255,255,255,0.08),transparent)] blur-2xl" />
        </div>

        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
          <div className="grid items-center gap-10 lg:grid-cols-12">
            <div className="lg:col-span-7">
              <motion.h1
                initial={{ opacity: 0, y: 10 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6 }}
                className="text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight"
              >
                We create, distribute & monetize content for brands and creators.
              </motion.h1>
              <p className="mt-5 max-w-2xl text-neutral-300 leading-relaxed">
                Odeka Media is a content studio and platform. We craft story‑first campaigns, produce original shows, and turn attention into revenue.
              </p>
              <div className="mt-8 flex flex-wrap gap-3">
                <a
                  href="#watch"
                  className="inline-flex items-center rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200"
                >
                  Watch on O'Channel
                </a>
                <a
                  href="#signin"
                  className="inline-flex items-center rounded-full border border-neutral-800 px-6 py-3 text-sm hover:border-neutral-700"
                >
                  Creator sign in
                </a>
                <a
                  href="#brief"
                  className="inline-flex items-center rounded-full border border-neutral-800 px-6 py-3 text-sm hover:border-neutral-700"
                >
                  Start a campaign
                </a>
              </div>
              <p className="mt-6 text-xs text-neutral-400">Trusted by advertisers, creators, and partners.</p>
            </div>
            <div className="lg:col-span-5">
              <div className="aspect-[4/3] w-full overflow-hidden rounded-3xl border border-neutral-800 bg-gradient-to-br from-neutral-900 to-neutral-800 p-2">
                <div className="h-full w-full rounded-2xl bg-neutral-950 relative">
                  {/* Replace with real preview image or video */}
                  <div className="absolute inset-0 grid place-items-center">
                    <div className="text-center">
                      <div className="mx-auto mb-4 h-12 w-12 rounded-xl bg-neutral-800" />
                      <p className="text-sm text-neutral-400">Promo reel placeholder</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function TrustBar() {
  return (
    <section className="border-y border-neutral-900/60">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
        {[{ k: "Shows", v: "6+" }, { k: "Monthly reach", v: "500K+" }, { k: "Avg. watch time", v: "3m 12s" }, { k: "Partners", v: "40+" }].map(
          (i) => (
            <div key={i.k}>
              <div className="text-2xl font-semibold">{i.v}</div>
              <div className="mt-1 text-xs text-neutral-400">{i.k}</div>
            </div>
          )
        )}
      </div>
    </section>
  );
}

function Advertisers() {
  return (
    <section id="advertisers" className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div className="grid gap-10 lg:grid-cols-12 items-start">
        <div className="lg:col-span-5">
          <h2 className="text-3xl sm:text-4xl font-semibold tracking-tight">Odeka for Advertisers</h2>
          <p className="mt-4 text-neutral-300">
            Story‑driven formats + measurable outcomes. Activate your brand across our shows and creator network, from quick‑turn promos to multi‑episode sponsorships.
          </p>
          <ul className="mt-6 space-y-3 text-neutral-300">
            <li>• Audience targeting: geo, interests, language (FR/EN/Eʋe)</li>
            <li>• Storytelling: native segments, integrations, product placement</li>
            <li>• Distribution: O'Channel, partners, paid boosts, owned placements</li>
            <li>• Measurement: view‑through, brand lift, conversions</li>
          </ul>
          <div className="mt-7 flex gap-3">
            <a href="#brief" className="rounded-full bg-white text-neutral-900 px-5 py-3 text-sm font-medium hover:bg-neutral-200">
              Submit a brief
            </a>
            <a href="#media-kit" className="rounded-full border border-neutral-800 px-5 py-3 text-sm hover:border-neutral-700">
              Download media kit
            </a>
          </div>
        </div>
        <div className="lg:col-span-7">
          <div className="grid sm:grid-cols-2 gap-6">
            {[
              { title: "Brand storytelling", desc: "Short‑form narratives produced by Odeka Studio — from teaser to hero film." },
              { title: "Creator partnerships", desc: "Tap trusted local voices to extend reach and authenticity." },
              { title: "Event coverage", desc: "On‑site capture + same‑day edits for festivals and launches." },
              { title: "Performance add‑ons", desc: "Retargeting, UTM tracking, A/B hooks, caption optimization." },
            ].map((c) => (
              <Card key={c.title} title={c.title} desc={c.desc} />
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function Channel() {
  const shows = [
    { name: "O'Show (flagship)", tag: "Interviews • Music • Culture", cta: "Watch episodes" },
    { name: "Street Stories", tag: "People • Places • Food", cta: "Watch episodes" },
    { name: "Creator Spotlight", tag: "Models • Creators • Makers", cta: "Watch episodes" },
    { name: "Business Now", tag: "Entrepreneurs • Playbooks", cta: "Watch episodes" },
  ];
  return (
    <section id="channel" className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div className="flex items-end justify-between gap-6">
        <h2 className="text-3xl sm:text-4xl font-semibold tracking-tight">O'Channel — Emissions</h2>
        <a href="#channel-all" className="text-sm text-neutral-300 hover:text-white">
          See all shows →
        </a>
      </div>
      <div className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {shows.map((s) => (
          <div
            key={s.name}
            className="group relative overflow-hidden rounded-3xl border border-neutral-900 bg-neutral-950"
          >
            <div className="aspect-video w-full bg-[linear-gradient(135deg,rgba(255,255,255,0.08),transparent)]" />
            <div className="p-5">
              <div className="text-lg font-medium">{s.name}</div>
              <div className="mt-1 text-sm text-neutral-400">{s.tag}</div>
              <div className="mt-4">
                <a href="#" className="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">
                  {s.cta}
                </a>
              </div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

function OShow() {
  return (
    <section id="oshow" className="relative overflow-hidden">
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-neutral-800 to-transparent" />
      </div>
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
        <div className="grid gap-10 lg:grid-cols-12 items-center">
          <div className="lg:col-span-6 order-2 lg:order-1">
            <h3 className="text-3xl sm:text-4xl font-semibold tracking-tight">O'Show — Star Emission</h3>
            <p className="mt-4 text-neutral-300">
              The centerpiece of our lineup. Long‑form interviews with artists and creators shaping culture. Available with sponsor integrations, live audience tapings, and short‑form cut‑downs.
            </p>
            <ul className="mt-6 space-y-3 text-neutral-300">
              <li>• Sponsorship tiers: opening tag, mid‑roll segment, end‑card</li>
              <li>• Deliverables: full episode + shorts + stills + captions</li>
              <li>• Options: live studio audience, giveaway, meet‑and‑greet</li>
            </ul>
            <div className="mt-7 flex gap-3">
              <a href="#sponsor-oshow" className="rounded-full bg-white text-neutral-900 px-5 py-3 text-sm font-medium hover:bg-neutral-200">
                Get sponsorship kit
              </a>
              <a href="#watch-oshow" className="rounded-full border border-neutral-800 px-5 py-3 text-sm hover:border-neutral-700">
                Watch latest episode
              </a>
            </div>
          </div>
          <div className="lg:col-span-6 order-1 lg:order-2">
            <div className="aspect-[16/10] w-full overflow-hidden rounded-3xl border border-neutral-900 bg-[radial-gradient(closest-side,rgba(255,255,255,0.10),transparent_70%)]" />
          </div>
        </div>
      </div>
    </section>
  );
}

function Campaigns() {
  const steps = [
    { name: "Brief", detail: "Objectives, audience, budget, target markets." },
    { name: "Story", detail: "Creative routes, scripts, casting, visual language." },
    { name: "Production", detail: "Studio or on‑location. Photo + video + design." },
    { name: "Distribution", detail: "O'Channel + creators + paid amplification." },
    { name: "Measurement", detail: "Analytics and brand lift study." },
  ];
  return (
    <section id="campaigns" className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div className="flex items-end justify-between gap-6">
        <h2 className="text-3xl sm:text-4xl font-semibold tracking-tight">Marketing campaigns with brand storytelling</h2>
        <a href="#case-study" className="text-sm text-neutral-300 hover:text-white">
          See case study →
        </a>
      </div>
      <div className="mt-10 grid gap-6 lg:grid-cols-12">
        <div className="lg:col-span-7">
          <div className="rounded-3xl border border-neutral-900 p-6 bg-neutral-950">
            <ol className="grid gap-6 lg:grid-cols-2">
              {steps.map((s, i) => (
                <li
                  key={s.name}
                  className="rounded-2xl border border-neutral-900/60 p-5 bg-[linear-gradient(180deg,rgba(255,255,255,0.04),transparent)]"
                >
                  <div className="text-sm text-neutral-400">Step {i + 1}</div>
                  <div className="mt-1 text-lg font-medium">{s.name}</div>
                  <p className="mt-2 text-sm text-neutral-300">{s.detail}</p>
                </li>
              ))}
            </ol>
            <div className="mt-6 text-sm text-neutral-400">
              We deliver media that feels native to the culture and the platform.
            </div>
          </div>
        </div>
        <div className="lg:col-span-5">
          <div className="rounded-3xl border border-neutral-900 bg-neutral-950 overflow-hidden">
            <div className="aspect-[4/3] bg-[linear-gradient(135deg,rgba(255,255,255,0.08),transparent)]" />
            <div className="p-6">
              <div className="text-lg font-medium">Case study — Local Launch</div>
              <p className="mt-2 text-sm text-neutral-300">
                4‑video story arc, creator collaborations, and paid boosts. Outcome example: +38% visits in 4 weeks, +12% repeat.
              </p>
              <div className="mt-4">
                <a href="#download-pdf" className="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">
                  Download PDF
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function CTA() {
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div className="overflow-hidden rounded-3xl border border-neutral-900 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),transparent)]">
        <div className="p-8 sm:p-12 lg:p-16 text-center">
          <h3 className="text-2xl sm:text-3xl font-semibold tracking-tight">Ready to launch your next campaign?</h3>
          <p className="mt-3 text-neutral-300">Send your brief — we’ll reply quickly with a proposal and timeline.</p>
          <div className="mt-6 flex flex-wrap justify-center gap-3">
            <a href="#brief" className="rounded-full bg-white text-neutral-900 px-6 py-3 text-sm font-medium hover:bg-neutral-200">
              Submit a brief
            </a>
            <a href="#call" className="rounded-full border border-neutral-800 px-6 py-3 text-sm hover:border-neutral-700">
              Book a call
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}

function Footer({ locale, currency, onChangeLocale, onChangeCurrency }) {
  const links = {
    Company: ["About", "Careers", "Contact"],
    Products: ["Odeka", "O'Channel", "O'Show", "Studio"],
    Advertisers: ["Media kit", "Pricing", "Case studies"],
    Legal: ["Privacy", "Terms"],
  };

  return (
    <footer id="contact" className="border-t border-neutral-900/60">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid gap-10 md:grid-cols-4">
          <div>
            <div className="flex items-center gap-3">
              <div className="h-7 w-7 rounded-xl bg-gradient-to-br from-white/80 via-white/30 to-white/0" />
              <span className="font-semibold tracking-tight">Odeka Media</span>
            </div>
            <p className="mt-4 text-sm text-neutral-400 max-w-xs">
              We create, distribute, and monetize content that moves culture.
            </p>
          </div>
          {Object.entries(links).map(([k, arr]) => (
            <div key={k}>
              <div className="text-sm font-medium text-neutral-300">{k}</div>
              <ul className="mt-3 space-y-2 text-sm text-neutral-400">
                {arr.map((v) => (
                  <li key={v}>
                    <a href="#" className="hover:text-white">
                      {v}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-8 grid gap-4 sm:flex sm:items-center sm:justify-between">
          {/* Language & currency controls */}
          <div className="flex flex-wrap items-center gap-4">
            <label htmlFor="odeka-lang" className="text-xs text-neutral-400">Language</label>
            <select
              id="odeka-lang"
              value={locale}
              onChange={(e) => onChangeLocale(e.target.value)}
              className="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700"
            >
              <option value="en-US">English</option>
              <option value="fr-FR">Français</option>
            </select>

            <label htmlFor="odeka-currency" className="ml-2 text-xs text-neutral-400">Currency</label>
            <select
              id="odeka-currency"
              value={currency}
              onChange={(e) => onChangeCurrency(e.target.value)}
              className="rounded-md border border-neutral-800 bg-neutral-950 px-2 py-1 text-xs text-neutral-200 hover:border-neutral-700"
            >
              <option value="XOF">CFA (XOF)</option>
              <option value="USD">USD</option>
              <option value="EUR">EUR</option>
            </select>
          </div>

          <div className="text-xs text-neutral-500">Made with care.</div>
        </div>

        <div className="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-500">
          <div>© {new Date().getFullYear()} Odeka Media. All rights reserved.</div>
          <div />
        </div>
      </div>
    </footer>
  );
}

function Access() {
  // Access hub: three clear entry points with strong information scent
  const entries = [
    {
      title: "Watch O'Channel",
      desc: "Open the platform to watch episodes and shorts.",
      cta: "Open platform",
      href: "#open-app",
    },
    {
      title: "Creators",
      desc: "Sign in to manage episodes, assets, and analytics.",
      cta: "Creator sign in",
      href: "#signin",
    },
    {
      title: "Advertisers",
      desc: "Start a story‑driven campaign with measurable outcomes.",
      cta: "Start a campaign",
      href: "#brief",
    },
  ];
  return (
    <section id="access" className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
      <div className="flex items-end justify-between gap-6">
        <h2 className="text-2xl sm:text-3xl font-semibold tracking-tight">Access the platform</h2>
      </div>
      <div className="mt-6 grid gap-6 md:grid-cols-3">
        {entries.map((e) => (
          <div key={e.title} className="rounded-3xl border border-neutral-900 bg-neutral-950 p-6">
            <div className="text-lg font-medium">{e.title}</div>
            <p className="mt-2 text-sm text-neutral-300">{e.desc}</p>
            <div className="mt-4">
              <a href={e.href} className="text-sm rounded-full border border-neutral-800 px-4 py-2 hover:border-neutral-700">
                {e.cta}
              </a>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

function TabSwitcher({ tab, onChangeTab }) {
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-2">
      <div className="md:hidden flex items-center justify-center">
        <div className="inline-flex items-center gap-2 p-1 rounded-full border border-neutral-800">
          {["Odeka", "Media"].map((label) => (
            <button
              key={label}
              onClick={() => onChangeTab(label)}
              aria-current={tab === label}
              className={
                "px-4 py-1.5 text-sm rounded-full transition " +
                (tab === label ? "bg-white text-neutral-900" : "text-neutral-300 hover:text-white")
              }
            >
              {label}
            </button>
          ))}
        </div>
      </div>
    </section>
  );
}

function OdekaTab({ locale, currency }) {
  return (
    <>
      <Advertisers />
      <EarningsSimulator locale={locale} currency={currency} />
      <Campaigns />
      <CTA />
    </>
  );
}

function MediaTab() {
  return (
    <>
      <TrustBar />
      <Channel />
      <OShow />
    </>
  );
}

function Card({ title, desc }) {
  return (
    <div className="rounded-3xl border border-neutral-900 bg-neutral-950 p-5">
      <div className="text-lg font-medium">{title}</div>
      <p className="mt-2 text-sm text-neutral-300">{desc}</p>
    </div>
  );
}

// --- Earnings Simulator (Creators) ---
function formatMoney(n, currency = "XOF", locale = "fr-FR") {
  try {
    return new Intl.NumberFormat(locale, { style: "currency", currency, maximumFractionDigits: 2 }).format(n);
  } catch (e) {
    return `${currency} ${Number(n).toFixed(2)}`;
  }
}

function EarningsSimulator({ locale = "fr-FR", currency = "XOF" }) {
  const [followers, setFollowers] = useState(300000);
  const [price, setPrice] = useState(1500); // per month (shown in selected currency)
  const conversion = 0.05; // 5% of followers subscribe (assumption)
  const platformFee = 0.05; // 5% fee deducted

  const subscribers = Math.round(followers * conversion);
  const gross = subscribers * price;
  const net = gross * (1 - platformFee);

  return (
    <section id="earnings" className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div className="text-center">
        <h2 className="text-3xl sm:text-4xl font-semibold tracking-tight">Creators Earnings Simulator</h2>
        <p className="mt-2 text-neutral-300">Estimate monthly revenue based on your audience and subscription price.</p>
      </div>

      <div className="mt-10 grid gap-10 lg:grid-cols-2">
        {/* Controls */}
        <div className="rounded-3xl border border-neutral-900 bg-neutral-950 p-6">
          <div className="flex items-center justify-between text-sm text-neutral-300">
            <label htmlFor="followers" className="font-medium">Number of followers?</label>
            <span className="tabular-nums text-neutral-400">#{followers.toLocaleString()}</span>
          </div>
          <input
            id="followers"
            type="range"
            min={0}
            max={1000000}
            step={1000}
            value={followers}
            onChange={(e) => setFollowers(Number(e.target.value))}
            className="mt-3 w-full accent-white"
          />

          <div className="mt-8 flex items-center justify-between text-sm text-neutral-300">
            <label htmlFor="price" className="font-medium">Monthly subscription price?</label>
            <span className="tabular-nums text-neutral-400">{formatMoney(price, currency, locale)}</span>
          </div>
          <input
            id="price"
            type="range"
            min={500}
            max={20000}
            step={100}
            value={price}
            onChange={(e) => setPrice(Number(e.target.value))}
            className="mt-3 w-full accent-white"
          />

          <div className="mt-6 text-xs text-neutral-400">
            Conversion assumed: {(conversion * 100).toFixed(0)}% of followers subscribe. Platform fee: {(platformFee * 100).toFixed(0)}% deducted. Payment processor fees not included.
          </div>
        </div>

        {/* Result */}
        <div className="rounded-3xl border border-neutral-900 bg-neutral-950 p-6 flex flex-col items-center justify-center text-center">
          <div className="text-neutral-300">Estimated subscribers</div>
          <div className="mt-1 text-2xl font-semibold tabular-nums">{subscribers.toLocaleString()}</div>

          <div className="mt-6 text-neutral-300">You could earn an estimated</div>
          <div className="mt-2 text-3xl sm:text-4xl font-semibold tracking-tight tabular-nums">
            {formatMoney(net, currency, locale)} <span className="text-neutral-400 text-xl align-middle">per month</span>
          </div>

          <div className="mt-6 text-xs text-neutral-500 max-w-md">
            * Estimate only. Based on {Math.round(conversion * 100)}% of followers who subscribe. Does not include payment processor fees. Net amount reflects a {Math.round(platformFee * 100)}% platform fee.
          </div>
        </div>
      </div>

      <div className="mt-8 text-center text-xs text-neutral-500">
        Want a custom plan for higher volumes or bundles? <a href="#contact" className="underline hover:text-white">Contact us</a>.
      </div>
    </section>
  );
}

/*
TEST CASES (React Testing Library style — place in a separate test file in real project)

1) Renders core sections and CTAs
- render(<OdekaHome />)
- expect(screen.getByText(/Odeka Media/i)).toBeInTheDocument()
- expect(screen.getByText(/We create, distribute & monetize/i)).toBeInTheDocument()
- expect(screen.getByRole('link', { name: /Watch on O'Channel/i })).toBeInTheDocument()
- expect(screen.getByRole('link', { name: /Creator sign in/i })).toBeInTheDocument()
- expect(screen.getByRole('link', { name: /Start a campaign/i })).toBeInTheDocument()

2) Access hub has three entries
- const cards = screen.getAllByText(/Open platform|Creator sign in|Start a campaign/i)
- expect(cards.length).toBe(3)

3) Tabs default to Odeka and switch to Media
- expect(screen.getByText(/Odeka for Advertisers/i)).toBeInTheDocument()
- user.click(screen.getByRole('button', { name: 'Media' }))
- expect(screen.getByText(/O'Channel — Emissions/i)).toBeInTheDocument()
- expect(screen.queryByText(/Odeka for Advertisers/i)).toBeNull()

4) No adjacent JSX root errors in Hero
- Ensure <Hero /> returns a single parent element (verified by successful render)

5) Professional copy (no personal locations)
- expect(screen.queryByText(/Lomé|Montreal|Canada|Togo/i)).toBeNull()

6) Header segmented control visible on desktop
- expect(screen.getByRole('button', { name: 'Odeka' })).toBeInTheDocument()
- expect(screen.getByRole('button', { name: 'Media' })).toBeInTheDocument()

7) Mobile TabSwitcher appears (hidden on md+)
- // Simulate small viewport or assert presence of the TabSwitcher container by test-id if you add one

8) Earnings Simulator is in Odeka tab (default) and not in Media
- expect(screen.getByText(/Creators Earnings Simulator/i)).toBeInTheDocument()
- user.click(screen.getByRole('button', { name: 'Media' }))
- expect(screen.queryByText(/Creators Earnings Simulator/i)).toBeNull()

9) Footer Products includes Odeka as the first item
- const productsColumn = screen.getByText(/Products/i).closest('div')
- expect(within(productsColumn).getAllByRole('link')[0]).toHaveTextContent(/^Odeka$/)

10) Language and Currency selectors exist in footer
- expect(screen.getByLabelText(/Language/i)).toBeInTheDocument()
- expect(screen.getByLabelText(/Currency/i)).toBeInTheDocument()

*/
