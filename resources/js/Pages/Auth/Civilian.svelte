<script>
    import Table from "@C/Temp/Table.svelte";
    import axios from "axios";

    const getData = async () => {
        const response = await axios.get(
            "http://127.0.0.1:8000/api/civiliant/all",
            {
                headers: {
                    Authorization:
                        "Bearer 1|XkakHOC5zI4ywAKaUvIuobcVbyqOXxaCUqj3AW6Zed59684b",
                },
            },
        );

        return response.data;
    };
</script>

<div class="w-full h-screen flex justify-center align-middle px-10 py-5">
    <div class="w-full h-[90%]">
        <div class="text-gray-900 bg-gray-200 rounded-lg">
            <div class="p-4 flex">
                <h1 class="text-3xl">Civilian</h1>
            </div>
            <div class="px-3 py-4 flex justify-center">
                <Table>
                    <tbody>
                        <tr class="border-b">
                            <th class="text-left p-3 px-5">ID</th>
                            <th class="text-left p-3 px-5">NIK</th>
                            <th class="text-left p-3 px-5">FULLNAME</th>
                            <th class="text-left p-3 px-5">BIRTH PLACE</th>
                            <th class="text-left p-3 px-5">BIRTH DATE</th>
                            <th class="text-left p-3 px-5">STATUS</th>
                            <th class="text-left p-3 px-5">FAMILY ID</th>
                            <th></th>
                        </tr>
                        {#await getData()}
                            <span class="w-full p-5">LOADING</span>
                        {:then data}
                            {#each data.data as { id, nik, fullName, birthplace, birthdate, residentstatus, family_id }, idx}
                                <tr
                                    class="border-b hover:bg-orange-100 bg-gray-100 text-center"
                                >
                                    <td class="p-3 px-5">
                                        <span>
                                            {id}
                                        </span>
                                    </td>
                                    <td class="p-3 px-5">
                                        <span>
                                            {nik}
                                        </span>
                                    </td>
                                    <td class="p-3 px-5 text-left">
                                        <span>
                                            {fullName}
                                        </span>
                                    </td>
                                    <td class="p-3 px-5">
                                        <span>
                                            {birthplace}
                                        </span>
                                    </td>
                                    <td class="p-3 px-5">
                                        <span>
                                            {new Date(
                                                birthdate * 1000,
                                            ).toDateString()}
                                        </span>
                                    </td>
                                    <td class="p-3 px-5">
                                        <span>
                                            {residentstatus}
                                        </span>
                                    </td>
                                    <td class="p-3 px-5">
                                        <span>
                                            {family_id}
                                        </span>
                                    </td>
                                    <td
                                        class="p-3 px-5 flex w-full h-full align-middle justify-center"
                                        ><button
                                            type="button"
                                            class="text-lg bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                        >
                                            Detail
                                        </button>
                                    </td></tr
                                >
                            {/each}
                        {/await}
                        <!-- <tr class="border-b hover:bg-orange-100 bg-gray-100">
                            <td class="p-3 px-5"
                                ><input
                                    type="text"
                                    value="user.name"
                                    class="bg-transparent"
                                /></td
                            >
                            <td class="p-3 px-5"
                                ><input
                                    type="text"
                                    value="user.email"
                                    class="bg-transparent"
                                /></td
                            >
                            <td class="p-3 px-5">
                                <select value="user.role" class="bg-transparent">
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                </select>
                            </td>
                            <td
                                class="p-3 px-5 flex w-full h-full align-middle justify-center"
                                ><button
                                    type="button"
                                    class="text-lg bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                >
                                    Detail
                                </button>
                            </td></tr
                        > -->
                    </tbody>
                </Table>
            </div>
        </div>
    </div>
</div>
