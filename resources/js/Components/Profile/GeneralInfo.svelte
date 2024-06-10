<script lang="ts">
    import { page } from "@inertiajs/svelte";

    import {
        Button,
        Card,
        Input,
        Label,
        Select,
        Textarea,
        Toast,
    } from "flowbite-svelte";
    import { createEventDispatcher, onMount } from "svelte";
    import axiosInstance from "axios";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    export let data: any;

    const user = $page.props.auth.user;
    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    let intro: string;
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };
    let residentStatus = [
        { value: "PermanentResident", name: "Penduduk asli" },
        { value: "ContractResident", name: "Kontrak" },
        { value: "Kos", name: "Kos" },
    ];

    const handleSubmitIntro = async () => {
        try {
            let body = {
                id: data.data[0].id,
                intro: intro,
                _token: $page.props.csrf_token,
            };

            const response = await axios.put("/api/user", body);

            err = response.data;
            dispatch("comp");

            console.log(response.data);

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        } catch (error) {
            err = {
                message: error?.response?.data?.message,
                status: error?.response?.data?.status,
            };
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(error);

            if (error?.response?.status == 401) {
                err = {
                    message: "Anda tidak memiliki izin",
                    status: false,
                };
                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);
            }
        }
    };

    onMount(() => {
        intro = user.intro;
    });
</script>

<Card class="max-w-full z-0">
    <h5
        class="mb-4 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
    >
        Informasi Umum
    </h5>
    {#if data.data?.length > 0}
        <div class="mb-4">
            <Label for="fullName" class="mb-2">Nama Lengkap</Label>
            <Input
                id="fullName"
                placeholder="Nama Lengkap"
                name="fullName"
                value={data.data[0].civilian_id.fullName}
                disabled
            />
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="residentstatus" class="mb-2"
                    >Status Kependudukan</Label
                >
                <Select
                    name="residentStatus"
                    placeholder="Status kependudukan"
                    disabled
                    class="z-0"
                >
                    {#each residentStatus as { value, name }, idx}
                        {#if value == data.data[0].civilian_id.residentstatus}
                            <option {value} selected>{name}</option>
                        {:else}
                            <option {value}>{name}</option>
                        {/if}
                    {/each}
                </Select>
            </div>
            <div class="mb-4">
                <Label for="job" class="mb-2">Pekerjaan</Label>
                <Input
                    id="job"
                    placeholder="Pekerjaan"
                    name="job"
                    value={data.data[0].civilian_id.job}
                    disabled
                />
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="nik" class="mb-2">NIK</Label>
                <Input
                    id="nik"
                    placeholder="NIK"
                    name="nik"
                    value={data.data[0].civilian_id.nik}
                    disabled
                />
            </div>
            <div class="mb-4">
                <Label for="nkk" class="mb-2">NKK</Label>
                <Input
                    id="nkk"
                    placeholder="123"
                    name="nkk"
                    value={data.data[0].civilian_id.nkk}
                    disabled
                />
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="address" class="mb-2">Alamat</Label>
                <Input
                    id="address"
                    placeholder="Alamat"
                    name="address"
                    value={data.data[0].civilian_id.address}
                    disabled
                />
            </div>
            <div class="mb-4">
                <Label for="no_hp" class="mb-2">No HP</Label>
                <Input
                    id="no_hp"
                    placeholder="No Hp"
                    name="phone"
                    value={data.data[0].civilian_id.phone}
                    disabled
                />
            </div>
        </div>

        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="">
                <Label for="birthdate" class="mb-2 w-full text-left"
                    >Tempat, Tanggal Lahir</Label
                >

                <div class="mb-4 grid max-xl:grid-cols-1 grid-cols-2 gap-2">
                    <Input
                        id="birthplace"
                        name="birthplace"
                        placeholder="Tempat Lahir"
                        value={data.data[0].civilian_id.birthplace}
                        disabled
                    />

                    <Input
                        id="birthdate"
                        placeholder="Tanggal Lahir"
                        type="date"
                        name="birthdate"
                        value={new Date(
                            data.data[0].civilian_id.birthdate * 1000,
                        )
                            .toISOString()
                            .substring(0, 10)}
                        disabled
                    />
                </div>
            </div>
            <div class="mb-4">
                <Label for="role" class="mb-2">Peran</Label>
                <Input
                    id="role"
                    disabled
                    placeholder="Peran"
                    name="role"
                    value={user.role}
                />
            </div>
            {#if user.role == "RW"}
                <div class="col-span-full">
                    <Label for="intro" class="mb-2">Kata Sambutan</Label>
                    <Textarea
                        id="intro"
                        placeholder="Sambutan"
                        name="intro"
                        bind:value={intro}
                    />
                    <div class="flex justify-end mt-2">
                        <Button on:click={handleSubmitIntro}
                            >Ubah Sambutan</Button
                        >
                    </div>
                </div>
            {/if}
        </div>
    {/if}
</Card>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
