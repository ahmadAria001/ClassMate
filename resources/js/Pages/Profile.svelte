<script lang="ts">
    import { Button, Card, Input, Label, Select } from "flowbite-svelte";
    import Layout from "./Layout.svelte";

    let selectedStatus: string;
    let residentStatus = [
        { value: "penduduk asli", name: "Penduduk asli" },
        { value: "kontrak", name: "Kontrak" },
        { value: "kos", name: "Kos" },
    ];
</script>

<Layout>
    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <Card class="max-w-full flex flex-row mb-6">
                <img
                    src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg"
                    alt="Foto profil"
                    class="w-24 h-24 rounded-lg mr-3"
                />
                <div>
                    <h5
                        class="font-bold text-black dark:text-white text-xl truncate max-w-xs md:max-w-md"
                    >
                        Muhammad Fatoni bisa gila Lorem ipsum dolor, sit amet
                        consectetur adipisicing elit. Eum, ab.
                    </h5>
                    <p class="font-light text-black dark:text-white">
                        13203242023
                    </p>
                    <Button size="sm" class="mt-2">
                        <svg
                            class="w-4 h-4 text-white mr-2"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 16"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                            />
                        </svg>
                        Upload Foto
                    </Button>
                </div>
            </Card>
            <h5
                class="mb-4 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Informasi Password
            </h5>
            <form action="" method="POST">
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="currentPassword" class="mb-2"
                            >Password Saat ini</Label
                        >
                        <Input
                            id="currentPassword"
                            placeholder="Password Saat ini"
                        />
                    </div>
                    <div class="mb-4">
                        <Label for="newPassword" class="mb-2"
                            >Password Baru</Label
                        >
                        <Input id="newPassword" placeholder="Password Baru" />
                    </div>
                </div>
                <div class="mb-4">
                    <Label for="confirmPassword" class="mb-2"
                        >Konfirmasi Password</Label
                    >
                    <Input
                        id="confirmPassword"
                        placeholder="Konfirmasi Password"
                    />
                </div>
                <div>
                    <h4
                        class="mb-4 text-md font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Persyaratan Password
                    </h4>
                    <ul
                        class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400"
                    >
                        <li>Minimal 10 karakter (dan hingga 100 karakter)</li>
                        <li>Gunakan kombinasi huruf besar dan kecil</li>
                        <li>
                            Gunakan minimal satu karakter khusus, misalnya, ! @
                            # ?
                        </li>
                    </ul>
                    <div class="block flex mt-3">
                        <Button type="submit" class="ml-auto"
                            >Simpan Perubahan</Button
                        >
                    </div>
                </div>
            </form>
        </div>
        <div>
            <Card class="max-w-full">
                <h5
                    class="mb-4 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
                >
                    Informasi Umum
                </h5>
                <form action="" method="POST">
                    <div class="mb-4">
                        <Label for="fullName" class="mb-2">Nama Lengkap</Label>
                        <Input id="fullName" placeholder="Nama Lengkap" />
                    </div>
                    <div class="grid md:grid-cols-2 md:gap-6">
                        <div class="mb-4">
                            <Label for="residentstatus" class="mb-2"
                                >Status Kependudukan</Label
                            >
                            <Select
                                items={residentStatus}
                                bind:value={selectedStatus}
                                placeholder="Status kependudukan"
                            />
                        </div>
                        <div class="mb-4">
                            <Label for="job" class="mb-2">Pekerjaan</Label>
                            <Input id="job" placeholder="Pekerjaan" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 md:gap-6">
                        <div class="mb-4">
                            <Label for="address" class="mb-2">Alamat</Label>
                            <Input id="address" placeholder="Alamat" />
                        </div>
                        <div class="mb-4">
                            <Label for="no_hp" class="mb-2">No Hp</Label>
                            <Input id="no_hp" placeholder="No Hp" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 md:gap-6">
                        <div class="mb-4">
                            <Label for="birthdate" class="mb-2"
                                >Tanggal Lahir</Label
                            >
                            <Input id="birthdate" placeholder="Tanggal Lahir" />
                        </div>
                        <div class="mb-4">
                            <Label for="role" class="mb-2">Peran</Label>
                            <Input id="role" disabled placeholder="Peran" />
                        </div>
                    </div>
                    <div class="block flex">
                        <Button type="submit" class="ml-auto"
                            >Simpan Perubahan</Button
                        >
                    </div>
                </form>
            </Card>
        </div>
    </div>
</Layout>
