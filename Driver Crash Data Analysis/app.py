import streamlit as st
import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt

import plotly.express as px
import json

st.set_page_config(layout='wide')
sns.set_theme(style="whitegrid")
st.markdown("<h1 style='text-align: center;'>🚗 Crash Data Analysis Dashboard</h1>", unsafe_allow_html=True)

@st.cache_data
def load_data():
    return pd.read_csv("crash_data.csv")

df = load_data()




# KPI Metrics
total_crashes = df['Crash_ID'].nunique()
total_fatalities = df['Count_Casualty_Fatality'].sum()
avg_impact = round((df['Count_Casualty_Fatality'] * 3 + df['Casualty_Count']).mean(), 2)
# Fatality Rate by Driver Category
fatality_rate_by_category = df.groupby('Driver_Category').apply(lambda x: (x['Count_Casualty_Fatality'].sum() / x['Count_Crashes'].sum()) * 100)
highest_fatality_rate_category = fatality_rate_by_category.idxmax()  # Category with the highest fatality rate


# Highest Fatality Age Group
fatalities_by_age_group = df.groupby('Casualty_AgeGroup')['Count_Casualty_Fatality'].sum()
highest_fatality_age_group = fatalities_by_age_group.idxmax()  # Age group with the highest fatality

#Primary Reason with Highest Crash Count
# Primary Reason with Highest Fatalities
fatalities_by_reason = df.groupby('Primary_Reason')['Count_Casualty_Fatality'].sum()
top_primary_reason_fatalities = fatalities_by_reason.idxmax() 

st.markdown("### 🔢 Key Crash Metrics")
col1, col2, col3, col4,col5 = st.columns(5)
col1.metric("🚗 Total Crashes", f"{total_crashes:,}")
col2.metric("💀 Total Fatalities", f"{int(total_fatalities):,}")
col3.metric("⚖️ Avg. Impact Score", avg_impact)
col4.metric("🚨 Highest Fatality Age group", highest_fatality_age_group)
col5.metric("💥 Primary Reason", top_primary_reason_fatalities)

st.sidebar.header("📅 Filter by Year")
years = sorted(df['Crash_Year'].dropna().unique())
selected_year = st.sidebar.selectbox("Select Year", years)
df = df[df['Crash_Year'] == selected_year]

tab1, tab2, tab3= st.tabs([
    "📊 Overview",
    "📉 Regional Analysis",
    "🔥 Risk & Impact"
])
with tab1:
    col1, col2 = st.columns(2)

    with col1:
        st.subheader("1️⃣ Fatalities by Driver Category")
        fig, ax = plt.subplots(figsize=(10, 6))
        sns.barplot(data=df, x='Driver_Category', y='Count_Casualty_Fatality', estimator=sum, ci=None, ax=ax)
        ax.set_ylabel("Fatalities")
        plt.xticks(rotation=45)
        plt.tight_layout()
        st.pyplot(fig)
        st.caption("📌 Unlicensed and Provisional drivers are involved in the most fatal crashes.")

    with col2:
        st.subheader("2️⃣ Casualties by Age Group")
        fig, ax = plt.subplots(figsize=(10, 6))
        sns.barplot(data=df, x='Casualty_AgeGroup', y='Casualty_Count', estimator=sum, ci=None, ax=ax)
        ax.set_ylabel("Casualty Count")
        plt.xticks(rotation=45)
        plt.tight_layout()
        st.pyplot(fig)
        st.caption("📌 Young drivers (16–24) are the most vulnerable casualty group.")

    with st.expander("👥 3️⃣ Casualties by Road User Type", expanded=True):
        fig, ax = plt.subplots(figsize=(12, 6))
        sns.barplot(data=df, x='Casualty_Road_User_Type', y='Casualty_Count', estimator=sum, ci=None, ax=ax)
        ax.set_ylabel("Casualty Count")
        ax.set_xlabel("Road User Type")
        plt.xticks(rotation=45)
        plt.tight_layout()
        st.pyplot(fig)
        st.caption("📌 Vehicle occupants and pedestrians show the highest casualty rates.")

    with st.expander("⚖️ 4️⃣ Casualty Severity by Gender", expanded=True):
        fig, ax = plt.subplots(figsize=(12, 6))
        sns.countplot(data=df, x='Casualty_Severity', hue='Casualty_Gender', ax=ax)
        ax.set_ylabel("Count")
        ax.set_xlabel("Severity Level")
        plt.xticks(rotation=45)
        plt.tight_layout()
        st.pyplot(fig)
        st.caption("📌 Severity levels are fairly balanced across gender, with slight male dominance in high-severity cases.")

with tab2:
    col3, col4 = st.columns(2)

    with col3:
        st.subheader("5️⃣ Casualties by Region")
        fig, ax = plt.subplots(figsize=(12, 8))
        sns.barplot(data=df, x='Location', y='Casualty_Count', estimator=sum, ci=None,
                    order=df.groupby('Location')['Casualty_Count'].sum().sort_values(ascending=False).index, ax=ax)
        ax.tick_params(axis='x', rotation=45)
        for label in ax.get_xticklabels():
            label.set_horizontalalignment('right')
        ax.set_ylabel("Casualty Count")
        plt.tight_layout()
        st.pyplot(fig)
        st.caption("📌 SEQ and Metro areas report the highest number of casualties.")

    with col4:
    # Don't use year-filtered df — use full data
        full_df = load_data()

    # Group data by Crash Year and sum Fatalities
    annual_fatalities = full_df.groupby('Crash_Year')['Count_Casualty_Fatality'].sum().reset_index()

    st.subheader("6️⃣ Annual Fatalities Trend")

    # Plot the Annual Fatalities Trend
    fig, ax = plt.subplots(figsize=(12, 8))
    sns.lineplot(data=annual_fatalities, x='Crash_Year', y='Count_Casualty_Fatality', marker='o', ax=ax)
    ax.set_ylabel("Fatalities Count")
    ax.set_xlabel("Year")
    plt.xticks(rotation=45)
    plt.tight_layout()
    
    # Show the plot
    st.pyplot(fig)
    
    # Add a caption for context
    st.caption("📌 Some years show spikes in fatalities; cross-referencing policy may help explain trends.")


with tab3:
    st.subheader("7️⃣ Crash Severity vs Driver Category")
    fig, ax = plt.subplots(figsize=(14, 8))
    ct = pd.crosstab(df['Driver_Category'], df['Crash_Severity'])
    sns.heatmap(ct, annot=True, fmt='d', cmap='YlOrRd', ax=ax, annot_kws={'fontsize': 10})
    ax.set_xlabel("Crash Severity")
    ax.set_ylabel("Driver Category")
    plt.tight_layout()
    st.pyplot(fig)
    st.caption("📌 Fatalities are concentrated among unlicensed and senior drivers.")

    st.subheader("8️⃣ Impact Score by Driver Category")
    df['Impact_Score'] = df['Count_Casualty_Fatality'] * 3 + df['Casualty_Count']
    fig, ax = plt.subplots(figsize=(14, 8))
    sns.barplot(data=df, x='Driver_Category', y='Impact_Score', estimator=sum, ci=None,
                order=df.groupby('Driver_Category')['Impact_Score'].sum().sort_values(ascending=False).index, ax=ax)
    ax.set_ylabel("Impact Score")
    ax.tick_params(axis='x', rotation=45)
    for label in ax.get_xticklabels():
        label.set_horizontalalignment('right')
    plt.tight_layout()
    st.pyplot(fig)
    st.caption("📌 Unlicensed drivers top the list in overall impact, combining fatal and non-fatal outcomes.")





with st.expander("🧠 Final Insights Summary", expanded=False):
    st.write("""
    - 🚩 **Unlicensed and Provisional Drivers** contribute most to fatal crashes.
    - 📍 **Metro and SEQ** regions are hotspots for high-impact incidents.
    - 📈 Fatalities trend varies year to year but needs more analysis for causality.
    - 👥 **16–24 year olds** are the most affected age group.
    - 🛑 **Road User Type** shows majority casualties from vehicle occupants and pedestrians.
    """)
