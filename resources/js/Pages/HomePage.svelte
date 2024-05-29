<script lang="ts">
    import Layout from "./Layout.svelte";
    import {
        A,
        Button,
        Card,
        Chart,
        Dropdown,
        DropdownItem,
        Popover,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
    } from "flowbite-svelte";
    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import { onMount } from "svelte";
    import {
        QuestionCircleSolid,
        UsersGroupOutline,
        FileImportOutline,
        DotsHorizontalOutline,
        ChevronDownOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";
    import CardInfo from "@C/HomePage/CardInfo.svelte";

    const axios = axiosInstance.create({ withCredentials: true });

    let role = $page.props.auth.user.role;
    let announcement: any;
    let citizenActivity: any;

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

    const getData = async () => {
        try {
            const [responseCivilian, responseDues, responseDocs] =
                await Promise.all([
                    axios.get(`/api/civilian/`),
                    axios.get(`/api/dues-payment`),
                    axios.get(`/api/docs/complaint`),
                ]);

            const countCivilian = responseCivilian.data;
            const countDues = responseDues.data;
            const countDocs = responseDocs.data;

            // Buat hitung total iuran
            const totalDues = countDues.data.reduce(
                (total: number, dues: number) => {
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

    const initData = async () => {
        data = await getData();
    };

    onMount(async () => {
        await initData();
        // console.log(data);
    });

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
                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white z-20"
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
                icon={UsersGroupOutline}
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
            <Card>
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

                <!-- <div
                    class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between"
                >
                    <div class="flex justify-end items-center pt-5">
                        <Button
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white bg-transparent hover:bg-transparent dark:bg-transparent dark:hover:bg-transparent focus:ring-transparent dark:focus:ring-transparent py-0"
                            >Last 7 days<ChevronDownOutline
                                class="w-2.5 m-2.5 ms-1.5"
                            /></Button
                        >
                        <Dropdown class="w-40">
                            <DropdownItem>Hari ini</DropdownItem>
                            <DropdownItem>1 Tahun Terakhir</DropdownItem>
                            <DropdownItem>3 Tahun Terakhir</DropdownItem>
                        </Dropdown>
                    </div>
                </div> -->
            </Card>
        </div>
    {/if}
</Layout>
