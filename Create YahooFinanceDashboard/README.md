# 📈 Yahoo Finance Stock Dashboard (ETL + Technical Analysis)

This project builds a Python-based ETL and Analytics pipeline for analyzing stock market trends using historical data fetched from the Yahoo Finance API (`yfinance`).

We extract 1-year historical stock prices, enrich the data with key technical indicators, perform sentiment analysis, and visualize financial insights for better investment decision-making.

---

## 📊 Key Features

- Real-time 1-year historical stock data fetching via `yfinance`
- Technical indicator calculations:
  - Simple Moving Averages (SMA 50, SMA 200)
  - Exponential Moving Average (EMA 50)
  - Relative Strength Index (RSI)
  - Moving Average Convergence Divergence (MACD)
  - Bollinger Bands
  - Daily Returns and Volatility
- Basic Sentiment Analysis using sample news headlines (TextBlob)
- Visualizations of stock price trends and technical signals
- End-to-end ETL process with feature engineering and data cleaning

---

## 📋 Project Structure

1. **Extract:** Fetch stock data for a user-provided ticker symbol.
2. **EDA:** Explore the raw data structure, check missing values, and generate basic statistics.
3. **Transform and Load:** Handle missing data, enrich dataset with technical indicators, and save enriched data to CSV.
4. **Analyze:** Generate visual insights through trendline plots and technical indicator graphs.
5. **Insights:** Summarize key observations from the analysis.
6. **Conclusion:** Wrap up findings and discuss future enhancement possibilities.

---

## 🛠 How to Run

1. Install required libraries:

    ```bash
    pip install yfinance pandas matplotlib seaborn textblob
    ```

2. Clone the repository or download the notebook.

3. Open the `yahoo_finance_stock_dashboard.ipynb` file.

4. Run all cells in order to:
   - Enter a stock ticker (e.g., AAPL, TSLA)
   - Fetch and enrich the data
   - Generate visual insights

---

## 🚀 Future Improvements

- Live sentiment fetching using News APIs
- Predictive modeling for stock price trends
- Portfolio-level multi-stock comparative analysis

---

## 📚 Skills Demonstrated

- Real-world ETL pipeline building
- Feature Engineering for Financial Data
- Exploratory Data Analysis (EDA)
- Financial Data Visualization
- Basic Sentiment Analysis Integration
- Clean project structuring and reproducibility

---

## 🧠 Key Outcome

Strengthened skills in financial data integration, trend analysis, and transparent reporting—key capabilities for audit, analytics, and risk-focused environments.

---
