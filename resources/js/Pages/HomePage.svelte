<script lang="ts">
    import Layout from "./Layout.svelte";
    import {
        Card,
        Chart,
        Popover,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
    } from "flowbite-svelte";

    import type { ApexOptions } from "apexcharts";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import { onMount } from "svelte";

    import CustomCard from "@C/General/CustomCard.svelte";
    import {
        QuestionCircleSolid,
        UsersGroupOutline,
        FileImportOutline,
        CashOutline,
    } from "flowbite-svelte-icons";
    import CardInfo from "@C/HomePage/CardInfo.svelte";

    const axios = axiosInstance.create({ withCredentials: true });

    let role = $page.props.auth.user.role;
    let id_rt = $page.props.auth.user.rt_id;
    let announcement: any;
    let citizenActivity: any;
    let income: any;
    let spendings: any;

    const getNews = async () => {
        const response = await axios.get(`/api/news/lts`, {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
    };

    const getCitizenEvents = async () => {
        const response = await axios.get(`/api/docs/activity/lts`);

        return response.data;
    };

    const getSpendings = async () => {
        const response = await axios.get(`/api/spending/monthly-income`);

        return response.data;
    };

    const getIncomes = async () => {
        const response = await axios.get(`/api/dues-payment/monthly-income`);

        return response.data;
    };

    const dateFormatter = (epoc: number) => {
        const date = new Date(epoc);

        const monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        const day = date.getDate();

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${day} ${monthName} ${year}`;
    };
    const formatterMonthYear = (epoc: number) => {
        const date = new Date(epoc);

        const monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${monthName} ${year}`;
    };

    onMount(async () => {
        announcement = await getNews();
        citizenActivity = await getCitizenEvents();
    });

    interface Data {
        civilianCount: number;
        totalDues: number;
        complaintCount: number;
    }

    interface Options {
        series: number[];
        colors: string[];
        chart: {
            height: number;
            width: string;
            type: string;
        };
        stroke: {
            colors: string[];
            lineCap: string;
        };
        plotOptions: {
            pie: {
                labels: {
                    show: boolean;
                };
                size: string;
                dataLabels: {
                    offset: number;
                };
            };
        };
        labels: string[];
        dataLabels: {
            enabled: boolean;
            style: {
                fontFamily: string;
            };
        };
        legend: {
            position: string;
            fontFamily: string;
            labels: {
                colors: string;
            };
        };
        yaxis: {
            labels: {
                formatter: (value: number) => string;
            };
        };
        xaxis: {
            labels: {
                formatter: (value: number) => string;
            };
            axisTicks: {
                show: boolean;
            };
            axisBorder: {
                show: boolean;
            };
        };
    }

    let data: Data | undefined;
    let colorApex: string;

    const options: Options = {
        series: [],
        colors: ["#1C64F2", "#16BDCA", "#9061F9"],
        chart: {
            height: 420,
            width: "100%",
            type: "pie",
        },
        stroke: {
            colors: ["white"],
            lineCap: "",
        },
        plotOptions: {
            pie: {
                labels: {
                    show: true,
                },
                size: "100%",
                dataLabels: {
                    offset: -25,
                },
            },
        },
        labels: ["Permananen", "Kontrak", "Kos"],
        dataLabels: {
            enabled: true,
            style: {
                fontFamily: "Inter, sans-serif",
            },
        },
        legend: {
            position: "bottom",
            fontFamily: "Inter, sans-serif",
            labels: {
                colors: "",
            },
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return value + "%";
                },
            },
        },
        xaxis: {
            labels: {
                formatter: function (value) {
                    return value + "%";
                },
            },
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
        },
    };

    let finance = {
        chart: {
            height: "400px",
            maxWidth: "100%",
            type: "line",
            fontFamily: "Inter, sans-serif",
            dropShadow: {
                enabled: false,
            },
            toolbar: {
                show: false,
            },
        },
        tooltip: {
            enabled: true,
            x: {
                show: false,
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            width: 6,
            curve: "smooth",
        },
        grid: {
            show: true,
            strokeDashArray: 4,
            padding: {
                left: 2,
                right: 2,
                top: -26,
            },
        },
        series: [
            {
                name: "Pemasukan",
                data: [6500, 6418, 6456, 6526, 6356, 6456],
                color: "#00A36C",
            },
            {
                name: "Pengeluaran",
                data: [6456, 6356, 6526, 6332, 6418, 6500],
                color: "#FF474C",
            },
        ],
        legend: {
            show: false,
        },
        xaxis: {
            categories: [
                "01 Feb",
                "02 Feb",
                "03 Feb",
                "04 Feb",
                "05 Feb",
                "06 Feb",
                "07 Feb",
            ],
            labels: {
                show: true,
                style: {
                    fontFamily: "Inter, sans-serif",
                    cssClass:
                        "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            show: false,
        },
    };

    const getData = async () => {
        try {
            const civilianUrl =
                role === "RT" || role === "Warga"
                    ? `/api/civilian/rt/${id_rt}`
                    : `/api/civilian`;
            const paymentUrl =
                role === "RT" || role === "Warga"
                    ? `/api/dues-payment/rt/${id_rt}`
                    : `/api/dues-payment`;
            const [responseCivilian, responseDues, responseDocs] =
                await Promise.all([
                    axios.get(civilianUrl),
                    axios.get(paymentUrl),
                    axios.get(`/api/docs/complaint/rt/1`),
                ]);

            const countCivilian = responseCivilian.data;
            const countDues = responseDues.data;
            const countDocs = responseDocs.data;

            // Buat hitung total iuran
            const totalDues = countDues.data.reduce(
                (total: number, dues: any) => {
                    return total + parseFloat(dues.amount_paid);
                },
                0,
            );

            // Buat hitung presentase resident
            const statusCounts = countCivilian.data.reduce(
                (acc: any, resident: any) => {
                    acc[resident.residentstatus] =
                        (acc[resident.residentstatus] || 0) + 1;
                    return acc;
                },
                {},
            );

            const permanentCount = statusCounts.PermanentResident || 0;
            const contractCount = statusCounts.ContractResident || 0;
            const flatCount = statusCounts.Kos || 0;
            const totalResident = permanentCount + contractCount + flatCount;

            // presentase resident
            const permanentPercent =
                totalResident > 0 ? (permanentCount / totalResident) * 100 : 0;
            const contractPercent =
                totalResident > 0 ? (contractCount / totalResident) * 100 : 0;
            const flatPercent =
                totalResident > 0 ? (flatCount / totalResident) * 100 : 0;

            // console.log(permanentPercent);
            // console.log(contractPercent);
            // console.log(flatPercent);

            // perbarui series options
            options.series = [permanentPercent, contractPercent, flatPercent];

            // console.log(countCivilian);
            // console.log(countDues);
            // console.log(countDocs);

            colorApex = document
                .getElementsByTagName("html")[0]
                .className.includes("dark")
                ? "white"
                : "black";

            options.legend.labels.colors = colorApex;

            // console.log(colorApex);

            return {
                civilianCount: countCivilian.data.length,
                totalDues: totalDues,
                complaintCount: countDocs.data.length,
            };
        } catch (error) {
            console.error(error);
            return { civilianCount: 0, totalDues: 0, complaintCount: 0 };
        }
    };

    const getSpendigMonthly = async () => {
        try {
            const response = await axios.get(`/api/spending/monthly-income`, {
                headers: {
                    Accept: "application/json",
                },
            });
            return response.data;
        } catch (error) {
            console.error("Error fetching spending log:", error);
        }
    };

    const getTotalDataDues = async () => {
        try {
            const response = await axios.get(`/api/dues-payment/rt/${id_rt}`, {
                headers: {
                    Accept: "application/json",
                },
            });

            // console.log("Dues payment response:", response);
            const paymentDues = response.data.data;
            // console.log("paymentDues:", paymentDues);

            if (Array.isArray(paymentDues)) {
                const totalDues = paymentDues.reduce(
                    (total: number, dues: any) => {
                        const amountPaid = parseFloat(dues.amount_paid);
                        // console.log("amountPaid:", amountPaid);
                        if (!isNaN(amountPaid)) {
                            return total + amountPaid;
                        } else {
                            console.error(
                                "Invalid amount_paid:",
                                dues.amount_paid,
                            );
                            return total;
                        }
                    },
                    0,
                );

                console.log("dues : " + response.data.data);
                return totalDues;
            } else {
                console.error("paymentDues is not an array or is undefined");
                return 0;
            }
        } catch (error) {
            console.error("Error fetching dues data:", error);
            return 0;
        }
    };

    const defineFinancesChart = () => {
        const dates: any[] = [];

        for (let index = 12 * 2; index >= 0; index--) {
            let date = new Date();
            date = new Date(date.setFullYear(2021));
            date = new Date(date.setMonth(12 - index));
            date = new Date(date.setDate(new Date().getDate()));

            const year = date.getFullYear();
            const month =
                date.getMonth() + 1 < 10
                    ? `0${date.getMonth() + 1}`
                    : date.getMonth() + 1;

            dates.push(`${year}-${month}`);
        }

        finance.series[0].data = [];
        finance.series[1].data = [];
        finance.xaxis.categories = [];

        dates.map((value: any) =>
            finance.xaxis.categories.push(formatterMonthYear(value)),
        );

        dates.map((value) => {
            // const inc = income.data.find((el: any) => el.month == value);
            // console.log(inc);
            finance.series[0].data.push(
                income.data.some((el: any) => el.month == value)
                    ? income.data.find((el: any) => el.month == value)
                          .total_amount
                    : 0,
            );
            finance.series[1].data.push(
                spendings.data.some((el: any) => el.month == value)
                    ? spendings.data.find((el: any) => el.month == value)
                          .total_amount
                    : 0,
            );
        });
    };

    const initData = async () => {
        data = await getData();
        income = await getIncomes();
        spendings = await getSpendings();

        defineFinancesChart();
    };

    let outcome: any;
    onMount(async () => {
        await initData();
        outcome = await getSpendigMonthly();

        // await getTotalDataDues();
        // console.log(data);
    });
</script>

<Layout>
    {#if role == "Warga"}
        <Card class="max-w-full">
            <h5
                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Pengumuman
            </h5>
            {#if announcement}
                {#each announcement.data as item, idx}
                    <h5 class="font-bold mt-2 text-black dark:text-white">
                        {item.title}
                    </h5>
                    <p class="mb-2">{item.desc}</p>
                    <hr />
                {/each}
            {/if}
        </Card>
        <Card class="mt-3 max-w-full">
            <h5
                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Agenda Kegiatan Masyarakat
            </h5>
            <Table>
                <TableHead>
                    <TableHeadCell>Nama</TableHeadCell>
                    <TableHeadCell>Lokasi</TableHeadCell>
                    <TableHeadCell class="text-center">Waktu</TableHeadCell>
                </TableHead>
                {#if citizenActivity}
                    <TableBody>
                        {#each citizenActivity.data as item, idx}
                            <TableBodyRow>
                                <TableBodyCell>
                                    <div
                                        class="flex justify-between align-middle gap-2"
                                    >
                                        <span class="w-full truncate">
                                            {item.name}
                                        </span>
                                        <QuestionCircleSolid
                                            id={`title-${item.id}`}
                                        />
                                    </div>
                                </TableBodyCell>
                                <TableBodyCell>
                                    {item.location}
                                </TableBodyCell>
                                <TableBodyCell
                                    class="text-center uppercase flex justify-center"
                                >
                                    <span class="text-center">
                                        {dateFormatter(item.startDate * 1000)}
                                        <br />
                                        {new Date(
                                            item.startDate * 1000,
                                        ).toLocaleTimeString(undefined, {
                                            hour12: false,
                                        })}
                                    </span>
                                    <span class="ms-5 text-center">
                                        {dateFormatter(item.endDate * 1000)}
                                        <br />
                                        {new Date(
                                            item.endDate * 1000,
                                        ).toLocaleTimeString(undefined, {
                                            hour12: false,
                                        })}
                                    </span>
                                </TableBodyCell>
                            </TableBodyRow>

                            <Popover
                                class="w-64 text-sm text-black dark:text-white z-50"
                                title="Deskripsi"
                                triggeredBy={`#title-${item.id}`}
                            >
                                <!-- {item.docs_id.description} -->
                                {item.name}
                            </Popover>
                        {/each}
                    </TableBody>
                {/if}
            </Table>
        </Card>
    {:else}
        <div
            class="info-card grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
        >
            <CardInfo
                title="Jumlah Warga"
                value={data?.civilianCount}
                icon={UsersGroupOutline}
                iconBgClass="bg-gray-900 dark:bg-gray-700"
            />
            <CardInfo
                title="Jumlah Iuran Terkumpul"
                value={data?.totalDues}
                icon={CashOutline}
                iconBgClass="bg-gray-900 dark:bg-gray-700"
            />
            <CardInfo
                title="Jumlah Laporan Masuk"
                value={data?.complaintCount}
                icon={FileImportOutline}
                iconBgClass="bg-gray-900 dark:bg-gray-700"
            />
        </div>

        <div class="chart-section">
            <div class="max-md:block flex gap-4 w-full">
                <CustomCard>
                    <div class="flex justify-between items-start w-full">
                        <div class="flex-col items-center">
                            <div class="flex items-center mb-1">
                                <h5
                                    class="text-xl font-bold leading-none text-gray-900 dark:text-white me-1"
                                >
                                    Persentase Status Rumah Warga
                                </h5>
                            </div>
                        </div>
                        <div class="flex justify-end items-center"></div>
                    </div>

                    <Chart {options} class="py-6 dark:text-white" />
                </CustomCard>
                <CustomCard divclass="flex-grow max-md:mt-2">
                    <div class="flex justify-between items-center w-full mb-2">
                        <div class="flex justify-center">
                            <h5
                                class="text-xl font-bold leading-none text-gray-900 dark:text-white me-1"
                            >
                                Grafik Keuangan
                            </h5>
                        </div>
                        <!-- <div>
                            <Button color="light" class="px-3 py-2"
                                >Last week<ChevronDownOutline
                                    class="w-2.5 h-2.5 ms-1.5"
                                /></Button
                            >
                            <Dropdown class="w-40">
                                <DropdownItem>Yesterday</DropdownItem>
                                <DropdownItem>Today</DropdownItem>
                                <DropdownItem>Last 7 days</DropdownItem>
                                <DropdownItem>Last 30 days</DropdownItem>
                                <DropdownItem>Last 90 days</DropdownItem>
                            </Dropdown>
                        </div> -->
                    </div>
                    <Chart options={finance} />
                </CustomCard>
            </div>
            <div class="div"></div>
        </div>
    {/if}
</Layout>
