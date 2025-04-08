import streamlit as st
import pandas as pd
import plotly.express as px
import json

# Load crash data
@st.cache_data
def load_data():
    crash = pd.read_csv("18ee2911-992f-40ed-b6ae-e756859786e6 (1).csv")
    casualty = pd.read_csv("177dc50c-0cf7-46ba-8a69-99695aeaa46a (1).csv")
    driver = pd.read_csv("dd13a889-2a48-4b91-8c64-59f824ed3d2c.csv")
    return crash, casualty, driver

# Load GeoJSON file
@st.cache_data
def load_geojson():
    with open("qps_regions.geojson") as f:
        return json.load(f)

crash_df, casualty_df, driver_df = load_data()
geojson = load_geojson()

# Dashboard selector (replacing slider with horizontal radio buttons for visual dashboard effect)
section = st.radio("\n", [
    "Executive Summary",
    "Severity & Cause Analysis",
    "Regional Patterns"
], horizontal=True)

st.title("🚦 Queensland Crash Data Dashboard")

# Executive Summary Section
if section == "Executive Summary":
    st.subheader("📍 Executive Summary")

    total_crashes = crash_df["_id"].nunique()
    total_fatalities = crash_df["Count_Fatality"].sum()
    total_casualties = casualty_df["Casualty_Count"].sum()
    total_hospitalised = crash_df["Count_Hospitalised"].sum()

    col1, col2, col3, col4 = st.columns(4)
    col1.metric("🚗 Crashes", f"{total_crashes:,}")
    col2.metric("💀 Fatalities", f"{int(total_fatalities):,}")
    col3.metric("🏥 Hospitalised", f"{int(total_hospitalised):,}")
    col4.metric("👥 Casualties", f"{int(total_casualties):,}")

    # Age Group Donut
    fig_age = px.pie(casualty_df, names="Casualty_AgeGroup", hole=0.4, title="Age Group Distribution")
    st.plotly_chart(fig_age, use_container_width=True)

    # Road User Types
    road_user = casualty_df.groupby("Casualty_Road_User_Type")[["Casualty_Count"]].sum().reset_index()
    fig_user = px.bar(road_user, x="Casualty_Road_User_Type", y="Casualty_Count", title="Road User Types")
    st.plotly_chart(fig_user, use_container_width=True)

    # Driver License Type Breakdown (check for existence)
    st.subheader("Driver Licence Info (if available)")
    st.write("(Skipped because column not found)")

# Severity & Cause Analysis Section
elif section == "Severity & Cause Analysis":
    st.subheader("💥 Severity & Cause Analysis")

    # Crash severity breakdown
    severity = crash_df.groupby("Crash_Severity")[["_id"]].count().reset_index()
    fig_sev = px.pie(severity, names="Crash_Severity", values="_id", title="Crash Severity Distribution")
    st.plotly_chart(fig_sev, use_container_width=True)

    # Year vs Fatalities trend
    year_fatal = crash_df.groupby("Crash_Year")[["Count_Fatality"]].sum().reset_index()
    fig_trend = px.line(year_fatal, x="Crash_Year", y="Count_Fatality", title="Fatalities Over Years", markers=True)
    st.plotly_chart(fig_trend, use_container_width=True)

    # Driver Category vs Severity (only if both columns exist)
    if "Driver_Category" in driver_df.columns and "Crash_Severity" in driver_df.columns:
        driver_cat = driver_df.groupby(["Driver_Category", "Crash_Severity"])["Count_Casualty_Fatality"].sum().reset_index()
        fig_driver = px.density_heatmap(driver_cat, x="Driver_Category", y="Crash_Severity", z="Count_Casualty_Fatality",
                                        title="Driver Category vs Crash Severity")
        st.plotly_chart(fig_driver, use_container_width=True)

# Regional Patterns Section
elif section == "Regional Patterns":
    st.subheader("🗺️ Regional Crash Patterns")

    region_names = crash_df["Crash_Police_Region"].dropna().unique().tolist()
    selected_region = st.selectbox("Select a Police Region:", ["All"] + sorted(region_names))

    # Filter crash and casualty data based on selected region
    filtered_crash_df = crash_df.copy()
    filtered_casualty_df = casualty_df.copy()
    if selected_region != "All":
        filtered_crash_df = filtered_crash_df[filtered_crash_df["Crash_Police_Region"].str.strip() == selected_region.strip()]
        filtered_casualty_df = filtered_casualty_df[filtered_casualty_df["Crash_PoliceRegion"].str.strip() == selected_region.strip()]

    # Aggregate fatalities by region (recalculated after filtering)
    region_fatal = filtered_crash_df.groupby("Crash_Police_Region")["Count_Fatality"].sum().reset_index()

    # Choropleth map
    if not region_fatal.empty:
        fig_map = px.choropleth_mapbox(region_fatal,
                                       geojson=geojson,
                                       locations="Crash_Police_Region",
                                       featureidkey="properties.REGION",
                                       color="Count_Fatality",
                                       mapbox_style="carto-positron",
                                       center={"lat": -22.5, "lon": 145},
                                       zoom=4.5,
                                       title="Fatalities by Police Region")
        st.plotly_chart(fig_map, use_container_width=True)

    # Casualties by region - show horizontal bar with consistent width and value labels
    if not filtered_casualty_df.empty:
        region_cas = filtered_casualty_df.groupby("Crash_PoliceRegion")["Casualty_Count"].sum().reset_index()
        fig_bar = px.bar(region_cas,
                         y="Crash_PoliceRegion",
                         x="Casualty_Count",
                         orientation="h",
                         text="Casualty_Count",
                         title="Casualties by Region",
                         height=400)
        fig_bar.update_layout(xaxis_range=[0, max(region_cas["Casualty_Count"]) * 1.1])
        st.plotly_chart(fig_bar, use_container_width=True)

        # Restraint Use Pie (after filtering)
        fig_restraint = px.pie(filtered_casualty_df, names="Casualty_Restraint_Helmet_Use", title="Safety Equipment Use")
        st.plotly_chart(fig_restraint, use_container_width=True)
    else:
        st.info("No casualty data available for this region.")
