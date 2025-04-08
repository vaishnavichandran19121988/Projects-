# 🚦 Queensland Crash Data Dashboard In progress



This interactive Streamlit dashboard provides deep insights into road crash statistics across Queensland. It uses publicly available crash and casualty datasets and visualizes key trends, patterns, and risks through storytelling elements.

---

## 📊 Features

### 🔹 Executive Summary
- Total crashes, fatalities, hospitalisations, and casualties
- Most vulnerable age group
- Region with highest fatalities
- Donut charts and bar charts showing age and road user distribution

### 🔹 Severity & Cause Analysis
- Crash severity breakdown
- Year-over-year fatalities trend
- Heatmap of driver category vs severity
- Treemap of primary crash causes
- Monthly fatalities timeline

### 🔹 Regional Patterns
- Choropleth map showing fatalities by police region
- Casualties bar chart per region (interactive)
- Restraint/helmet usage pie chart
- Region dropdown with auto-filtering
- Download button for region-level stats

---

## 🗂️ Data Sources

- `18ee2911...csv` – Crash data
- `177dc50c...csv` – Casualty data
- `dd13a889...csv` – Driver data
- `qps_regions.geojson` – Regional boundaries (for mapping)

---

## 🚀 Running the App

### Step 1: Install requirements
```bash
pip install streamlit pandas plotly

