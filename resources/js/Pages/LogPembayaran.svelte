<script lang="ts">
    import Layout from "./Layout.svelte";
    import { onMount } from "svelte";
    import axiosInstance from "axios";
    import {
        Button,
        Card,
        Table,
        Input,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Badge,
    } from "flowbite-svelte";
    import {
        UsersGroupOutline,
        FileImportOutline,
    } from "flowbite-svelte-icons";
    import { Select, Label } from "flowbite-svelte";
    import { page } from "@inertiajs/svelte";
    import CardInfo from "@C/HomePage/CardInfo.svelte";

    const axios = axiosInstance.create();

    let role: string = $page.props.auth.user.role;
    let rt_id: string = $page.props.auth.user.rt_id;
    let value: string;
    let disabledSelectRT: boolean = false;

    if (role === "RT") {
        disabledSelectRT = true;
        value = rt_id;
    } else {
        value = "";
    }

    interface Civil {
        id: number;
        nik: string;
        fullName: string;
        birthplace: string;
        birthdate: number;
        residentstatus: string;
        nkk: string;
        isFamilyHead: number;
        rt_id: number;
        address: string;
        status: string;
        phone: string;
        religion: string;
        job: string;
        created_at: string;
        created_by: number;
        updated_at: string | null;
        updated_by: number | null;
        deleted_at: string | null;
        deleted_by: number | null;
    }

    interface RT {
        id: number;
        leader_id: number | null;
        created_at: string;
        created_by: number;
        number: number;
        updated_at: string | null;
        updated_by: number | null;
        deleted_at: string | null;
        deleted_by: number | null;
        civils: Civil[];
    }

    interface RTFormatted {
        id: number;
        name: string;
    }

    let dataRT: RTFormatted[] = [];
    let data: string;
    // const token = getCookie("token");
    // if (!token) {
    //     console.error("No token found");
    //     throw new Error("No token found");
    // }

    const getData = async () => {
        try {
            const response = await axios.get("api/civilian/", {
                headers: {
                    Accept: "application/json",
                },
            });

            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const initData = async () => {
        data = await getData();
    };

    const getRTData = async (): Promise<RTFormatted[]> => {
        const response = await axios.get("/api/rt", {
            headers: {
                Accept: "*/*",
            },
        });

        console.log("API RT Data Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((rt: RT) => ({
                id: rt.number,
                name: `RT ${rt.number}`,
            }));
        } else {
            throw new Error("Unexpected response format");
        }
    };

    onMount(async () => {
        try {
            dataRT = await getRTData();
            await initData();
            // console.log(dataRT);
            console.log(data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    function handleSelectChange() {}
</script>

<Layout>
    <Badge>Masih dummy static</Badge>
    <!-- <div
        class="info-card grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
    >
        <CardInfo
            title="Total Pemasukan"
            value="10000000"
            icon={UsersGroupOutline}
            iconBgClass="bg-gray-900 dark:bg-gray-700"
            textColor="text-green-500"
        />
        <CardInfo
            title="Total Pengeluaran"
            value="50000"
            icon={UsersGroupOutline}
            iconBgClass="bg-gray-900 dark:bg-gray-700"
            textColor="text-red-500"
        />
        <CardInfo
            title="Pemasukan Tahun ini"
            value="200000"
            icon={FileImportOutline}
            iconBgClass="bg-gray-900 dark:bg-gray-700"
            textColor="text-green-500"
        />
        <CardInfo
            title="Pengeluaran Tahun ini"
            value="100000"
            icon={FileImportOutline}
            iconBgClass="bg-gray-900 dark:bg-gray-700"
            textColor="text-red-500"
        />
    </div> -->

    <div
        class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md flex flex-col w-full"
    >
        <div class="w-full text-end flex justify-between p-4">
            <p class="text-2xl font-bold text-black">Catatan Pengeluaran</p>
        </div>
        <Table tableClass="mb-0">
            <TableHead>
                <TableHeadCell>Nama Lengkap</TableHeadCell>
                <TableHeadCell>Nama Petugas</TableHeadCell>
                <!-- <TableHeadCell class="text-center"
                    >Status Kependudukan</TableHeadCell
                > -->
                <TableHeadCell class="text-center">Tanggal Bayar</TableHeadCell>
                <TableHeadCell class="text-center">Bulan Bayar</TableHeadCell>
                <TableHeadCell class="text-center">Status</TableHeadCell>
                <!-- <TableHeadCell>
                    <span class="sr-only">Edit</span>
                </TableHeadCell> -->
            </TableHead>
            <TableBody tableBodyClass="divide-y">
                <!-- dummy -->
                <TableBodyRow>
                    <TableBodyCell>Joko Anwar</TableBodyCell>
                    <TableBodyCell>Nuri Darmayanti</TableBodyCell>
                    <!-- <TableBodyCell class="text-center">
                        <Badge color="green">Tetap</Badge>
                    </TableBodyCell> -->
                    <TableBodyCell class="text-center">30-5-2024</TableBodyCell>
                    <TableBodyCell class="text-center">Juni</TableBodyCell>
                    <TableBodyCell class="text-center">
                        <Badge color="green">Lunas</Badge>
                    </TableBodyCell>
                    <!-- <TableBodyCell>
                        <Button href="/iuran/detail">Detail</Button>
                    </TableBodyCell> -->
                </TableBodyRow>
                <!-- end dummy -->
            </TableBody>
        </Table>
    </div>
</Layout>
